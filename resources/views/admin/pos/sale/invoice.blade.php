@extends('admin.layout.app')

@section('title', 'Sale Detail — ' . ($sale->invoice_number ?? '#'.$sale->id))

@section('content')
<style>
    html, body { max-width: 100%; overflow-x: hidden !important; }
    .invoice-page { margin-left: 0 !important; width: 100% !important; max-width: 100vw !important; }
    .invoice-page, .invoice-page .content { min-width: 0 !important; max-width: 100% !important; overflow-x: hidden; }
    .invoice-page .content { padding-left: 24px !important; padding-right: 24px !important; }
    .invoice-shell { max-width: none; width: 100%; min-width: 0; margin: 0; overflow: hidden; }
    .invoice-toolbar { gap: .5rem; flex-wrap: wrap; min-width: 0; }
    .invoice-actions { display: flex; justify-content: flex-end; gap: .5rem; flex-wrap: wrap; min-width: 0; }
    #invoice .card { border: 0; border-radius: 16px; box-shadow: 0 8px 24px rgba(17, 55, 92, .08); overflow: hidden; }
    #invoice .card-header { background: linear-gradient(135deg, #07579f, #0d79c7); color: #fff; border: 0; padding: 1rem 1.25rem; }
    #invoice .card-header .card-title { margin: 0; font-weight: 700; }
    #invoice .table thead th { background: #edf6ff; color: #12558d; border: 0; font-size: .78rem; text-transform: uppercase; letter-spacing: .03em; }
    #invoice .table td { vertical-align: middle; border-color: #edf2f7; overflow-wrap: anywhere; }
    #invoice .table-wrap { max-width: 100%; overflow-x: auto; }
    #invoice .table-wrap > table { width: 100%; table-layout: fixed; }
    .invoice-total { font-size: 1.35rem; color: #07579f; }
    .invoice-label { color: #7a8a9a; font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; }
    .invoice-value { color: #17324d; font-weight: 600; }
    .invoice-brand { display: flex; align-items: center; gap: .75rem; }
    .invoice-brand img { width: 42px; height: 42px; object-fit: contain; border-radius: 10px; background: #fff; padding: 3px; }
    @media (max-width: 900px) {
        .invoice-toolbar > h4 { width: 100%; }
        .invoice-actions { width: 100%; justify-content: flex-start; }
        .invoice-actions .btn, .invoice-actions form { flex: 1 1 auto; }
        .invoice-actions form .btn { width: 100%; }
    }
    @media (max-width: 767px) {
        .invoice-shell > .row > [class*="col-md-"],
        #invoice > .row > [class*="col-md-"] { flex: 0 0 100%; max-width: 100%; }
        #invoice .card-body { padding: .85rem; }
        #invoice .card-header { padding: .85rem 1rem; }
        .invoice-brand img { width: 36px; height: 36px; }
    }
    @media (max-width: 768px) {
        .invoice-toolbar .btn { flex: 1 1 auto; }
        #invoice .table-wrap > table { min-width: 820px; }
    }
    @media print {
        #invoice, #invoice * { visibility: visible !important; }
        .main-header, .sidebar, .navbar, .topbar, .footer, .invoice-toolbar,
        .main-panel > .content > .container-fluid > .d-flex > div { display: none !important; }
        .main-panel { margin-left: 0 !important; width: 100% !important; }
        .content, .container-fluid { padding: 0 !important; margin: 0 !important; }
        #invoice .card { box-shadow: none; }
    }
</style>
<div class="main-panel invoice-page">
    <div class="content">
        <div class="container-fluid invoice-shell">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3 invoice-toolbar">
                <h4 class="page-title mb-0">Sale Detail</h4>
                <div class="invoice-actions">
                    <a href="{{ url('admin-panel/sales/listing') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    <button class="btn btn-primary print"><i class="fas fa-print"></i> Print</button>
                    <a href="{{ url('admin-panel/pos/pdf', $sale->id) }}" class="btn btn-secondary"><i class="fas fa-file-pdf"></i> Download PDF</a>
                    @if(!in_array($sale->status, ['cancelled', 'void']))
                    <form action="{{ url('admin-panel/sales/delete', $sale->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin membatalkan sale ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Cancel Sale</button>
                    </form>
                    @endif
                </div>
            </div>

            <div id="invoice">
            {{-- Sale Header Card --}}
            <div class="card mb-3">
                <div class="card-header">
                    <div class="invoice-brand">
                        <img src="{{ asset('admin/images/retail-point-of-sale-logo.png') }}" alt="Retail Point Of Sale">
                        <div>
                            <div class="card-title">Retail Point Of Sale</div>
                            <small>Transaction Information</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="invoice-label">Invoice Number</div>
                            <div class="invoice-value">{{ $sale->invoice_number ?? '#'.$sale->id }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="invoice-label">Transaction Date</div>
                            <div class="invoice-value">{{ $sale->created_at?->format('d M Y H:i') }}</div>
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
                            <div class="invoice-label">Store</div>
                            <div class="invoice-value">{{ optional($sale->store)->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-label">Cashier</div>
                            <div class="invoice-value">{{ optional($sale->user)->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-label">Customer</div>
                            <div class="invoice-value">{{ optional($sale->customer)->name ?? 'Walk-in' }}</div>
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
                <div class="card-body p-0 table-wrap">
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
                                    <td class="text-right"><strong class="invoice-total">Rp {{ number_format($sale->total ?? 0, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Paid</td>
                                    <td class="text-right">Rp {{ number_format($sale->paid_amount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Change</td>
                                    <td class="text-right">Rp {{ number_format($sale->change_amount ?? 0, 0, ',', '.') }}</td>
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
