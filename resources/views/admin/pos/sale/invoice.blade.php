@extends('admin.layout.app')

@section('title', 'Sale Detail — ' . ($sale->invoice_number ?? '#'.$sale->id))

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-title mb-0">Sale Detail</h4>
                <div>
                    <a href="{{ url('admin-panel/sales/listing') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    <button class="btn btn-primary print"><i class="fas fa-print"></i> Print</button>
                    <a href="{{ url('admin-panel/pos/pdf', $sale->id) }}" class="btn btn-secondary"><i class="fas fa-file-pdf"></i> Download PDF</a>
                    @if(!in_array($sale->status, ['cancelled', 'void']))
                    <form action="{{ url('admin-panel/sales/delete', $sale->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin membatalkan sale ini?');">
                        @csrf
                        <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Cancel Sale</button>
                    </form>
                    @endif
                </div>
            </div>

            <div id="invoice">
            {{-- Sale Header Card --}}
            <div class="card mb-3">
                <div class="card-header"><div class="card-title">Transaction Information</div></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Invoice Number</small>
                            <div><strong>{{ $sale->invoice_number ?? '#'.$sale->id }}</strong></div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Transaction Date</small>
                            <div>{{ $sale->created_at?->format('d M Y H:i') }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Sale Status</small>
                            <div>
                                @php
                                    $ssBadge = match($sale->status) {
                                        'completed' => 'badge-success',
                                        'cancelled', 'void' => 'badge-danger',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $ssBadge }}">{{ ucfirst($sale->status) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Payment Status</small>
                            <div>
                                @php
                                    $psBadge = match($sale->payment_status ?? 'UNPAID') {
                                        'PAID' => 'badge-success',
                                        'PARTIAL' => 'badge-warning',
                                        'REFUNDED' => 'badge-info',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $psBadge }}">{{ $sale->payment_status ?? 'UNPAID' }}</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">Store</small>
                            <div>{{ optional($sale->store)->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Cashier</small>
                            <div>{{ optional($sale->user)->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Customer</small>
                            <div>{{ optional($sale->customer)->name ?? 'Walk-in' }}</div>
                            @if($sale->customer)
                            <small>{{ $sale->customer->phone ?? '' }} {{ $sale->customer->email ? '| '.$sale->customer->email : '' }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="card mb-3">
                <div class="card-header"><div class="card-title">Items ({{ $sale->sale_items->count() }})</div></div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>SKU</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">Tax</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->sale_items as $i => $it)
                                @php
                                    $lineBase = ($it->price ?? 0) * ($it->quantity ?? 0);
                                    $lineDiscount = ($it->discount ?? 0) * ($it->quantity ?? 0);
                                    $lineTotal = $lineBase - $lineDiscount + ($it->tax ?? 0);
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ optional($it->product)->name ?? '-' }}</td>
                                    <td>{{ optional($it->product_variant)->name ?? '-' }}</td>
                                    <td>{{ optional($it->product)->sku ?? '-' }}</td>
                                    <td class="text-right">{{ $it->quantity }}</td>
                                    <td class="text-right">{{ number_format($it->price ?? 0, 2) }}</td>
                                    <td class="text-right">{{ number_format($it->discount ?? 0, 2) }}</td>
                                    <td class="text-right">{{ number_format($it->tax ?? 0, 2) }}</td>
                                    <td class="text-right">{{ number_format($lineTotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Summary & Payment --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Payment</div></div>
                        <div class="card-body">
                            @forelse($sale->payments as $payment)
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <span class="badge badge-primary">{{ optional($payment->method)->name ?? '-' }}</span>
                                    @if($payment->reference)
                                    <small class="text-muted ml-2">Ref: {{ $payment->reference }}</small>
                                    @endif
                                </div>
                                <div><strong>{{ number_format($payment->amount ?? 0, 2) }}</strong></div>
                            </div>
                            <small class="text-muted">
                                Status: {{ $payment->status ?? '-' }} |
                                Date: {{ $payment->created_at?->format('d M Y H:i') }}
                            </small>
                            @empty
                            <p class="text-muted mb-0">Belum ada pembayaran.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Summary</div></div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-right">{{ number_format($sale->subtotal ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td class="text-right">-{{ number_format($sale->discount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Tax</td>
                                    <td class="text-right">{{ number_format($sale->tax ?? 0, 2) }}</td>
                                </tr>
                                <tr style="border-top:2px solid var(--color-blue-200);">
                                    <td><strong>Grand Total</strong></td>
                                    <td class="text-right"><strong>{{ number_format($sale->total ?? 0, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Paid</td>
                                    <td class="text-right">{{ number_format($sale->paid_amount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Change</td>
                                    <td class="text-right">{{ number_format($sale->change_amount ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Audit --}}
            <div class="card mb-3">
                <div class="card-header"><div class="card-title">Audit</div></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Created By</small>
                            <div>{{ optional($sale->user)->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Created At</small>
                            <div>{{ $sale->created_at?->format('d M Y H:i:s') }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Updated At</small>
                            <div>{{ $sale->updated_at?->format('d M Y H:i:s') }}</div>
                        </div>
                        @if($sale->cancelled_at)
                        <div class="col-md-3">
                            <small class="text-muted">Cancelled At</small>
                            <div>{{ $sale->cancelled_at->format('d M Y H:i:s') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            </div> {{-- end #invoice --}}

        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="{{ asset('admin/pos/custom.js') }}"></script>
@endpush
