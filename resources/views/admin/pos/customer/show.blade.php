@extends('admin.layout.app')

@section('title', 'Customer Detail — ' . $customer->name)

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-title mb-0">Customer Detail</h4>
                <div>
                    <a href="{{ url('admin-panel/customer/listing') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    <a href="{{ url('admin-panel/customer/edit', $customer->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                </div>
            </div>

            <div class="row">
                {{-- Customer Information --}}
                <div class="col-md-5">
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Customer Information</div></div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr><td style="width:40%"><strong>Customer Code</strong></td><td>{{ $customer->customer_code ?? '-' }}</td></tr>
                                <tr><td><strong>Name</strong></td><td>{{ $customer->name }}</td></tr>
                                <tr><td><strong>Phone</strong></td><td>{{ $customer->phone ?? '-' }}</td></tr>
                                <tr><td><strong>Email</strong></td><td>{{ $customer->email ?? '-' }}</td></tr>
                                <tr><td><strong>Address</strong></td><td>{{ $customer->address ?? '-' }}</td></tr>
                                <tr><td><strong>Created</strong></td><td>{{ $customer->created_at?->format('d M Y H:i') }}</td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Summary Stats --}}
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Summary</div></div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h3>{{ $customer->total_transactions }}</h3>
                                    <small class="text-muted">Total Transactions</small>
                                </div>
                                <div class="col-6">
                                    <h3>{{ number_format($customer->total_spend, 2) }}</h3>
                                    <small class="text-muted">Total Spend</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sales History --}}
                <div class="col-md-7">
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Sales History</div></div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>Store</th>
                                        <th class="text-right">Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->sales as $sale)
                                    <tr>
                                        <td><strong>{{ $sale->invoice_number ?? '#'.$sale->id }}</strong></td>
                                        <td>{{ $sale->created_at?->format('d M Y') }}</td>
                                        <td>{{ optional($sale->store)->name ?? '-' }}</td>
                                        <td class="text-right">{{ number_format($sale->total, 2) }}</td>
                                        <td>
                                            @php
                                                $ssBadge = match($sale->status) {
                                                    'completed' => 'badge-success',
                                                    'cancelled', 'void' => 'badge-danger',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $ssBadge }}">{{ ucfirst($sale->status) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $psBadge = match($sale->payment_status ?? 'UNPAID') {
                                                    'PAID' => 'badge-success',
                                                    'PARTIAL' => 'badge-warning',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $psBadge }}">{{ $sale->payment_status ?? 'UNPAID' }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ url('admin-panel/pos/invoice', $sale->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-3">Belum ada transaksi.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
