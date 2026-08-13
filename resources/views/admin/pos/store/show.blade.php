@extends('admin.layout.app')

@section('title', 'Store Detail — ' . $store->name)

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-title mb-0">Store Detail</h4>
                <div>
                    <a href="{{ url('admin-panel/stores/listing') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    <a href="{{ url('admin-panel/stores/edit', $store->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                </div>
            </div>

            <div class="row">
                {{-- Store Information --}}
                <div class="col-md-5">
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Store Information</div></div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr><td style="width:40%"><strong>Store Code</strong></td><td>{{ $store->store_code ?? $store->code ?? '-' }}</td></tr>
                                <tr><td><strong>Store Name</strong></td><td>{{ $store->name }}</td></tr>
                                <tr><td><strong>Created</strong></td><td>{{ $store->created_at?->format('d M Y H:i') }}</td></tr>
                                <tr><td><strong>Updated</strong></td><td>{{ $store->updated_at?->format('d M Y H:i') }}</td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Summary Stats --}}
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Summary</div></div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h3>{{ $store->sales()->count() }}</h3>
                                    <small class="text-muted">Total Sales</small>
                                </div>
                                <div class="col-6">
                                    <h3>{{ number_format($store->sales()->where('status', 'completed')->sum('total'), 2) }}</h3>
                                    <small class="text-muted">Revenue</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Sales --}}
                <div class="col-md-7">
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Recent Sales (Last 10)</div></div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th class="text-right">Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($store->sales()->latest()->take(10)->get() as $sale)
                                    <tr>
                                        <td><strong>{{ $sale->invoice_number ?? '#'.$sale->id }}</strong></td>
                                        <td>{{ $sale->created_at?->format('d M Y') }}</td>
                                        <td>{{ optional($sale->customer)->name ?? 'Walk-in' }}</td>
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
                                            <a href="{{ url('admin-panel/pos/invoice', $sale->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3">Belum ada transaksi dari store ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($store->sales()->count() > 10)
                        <div class="card-footer text-center">
                            <a href="{{ url('admin-panel/sales/listing') }}" class="btn btn-sm btn-outline-primary">View All Sales</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
