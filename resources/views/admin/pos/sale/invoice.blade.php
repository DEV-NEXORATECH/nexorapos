@extends('admin.layout.app')

@section('title', 'Sale Detail — ' . ($sale->invoice_number ?? '#'.$sale->id))

@section('content')
<style>
    .invoice-shell { max-width: 100%; width: 100%; }
    .invoice-toolbar { gap: .5rem; flex-wrap: wrap; }
    .invoice-actions { display: flex; justify-content: flex-end; gap: .5rem; flex-wrap: wrap; }
    #invoice .card { border: 1px solid #E2E8F0; border-radius: 14px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05); overflow: hidden; background: #fff; }
    #invoice .card-header { background: linear-gradient(135deg, #0058A6, #003F7D); color: #fff; border: 0; padding: 1rem 1.25rem; }
    #invoice .card-header .card-title { margin: 0; font-weight: 700; color: #fff; font-size: 15px; }
    #invoice .table thead th { background: #F1F5F9; color: #334155; border: 0; font-size: 12px; text-transform: uppercase; letter-spacing: .03em; font-weight: 700; }
    #invoice .table td { vertical-align: middle; border-color: #F1F5F9; font-size: 13px; color: #1E293B; }
    #invoice .table-wrap { max-width: 100%; overflow-x: auto; }
    .invoice-total { font-size: 1.5rem; color: #0058A6; font-weight: 800; }
    .invoice-label { color: #64748B; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; font-weight: 700; margin-bottom: 2px; }
    .invoice-value { color: #0F172A; font-weight: 700; font-size: 14px; }
    .invoice-brand { display: flex; align-items: center; gap: .75rem; }
    
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
    }
    @media (max-width: 768px) {
        .invoice-toolbar .btn { flex: 1 1 auto; }
        #invoice .table-wrap > table { min-width: 820px; }
    }
    @media print {
        html, body {
            visibility: visible !important;
            overflow: visible !important;
            background: #fff !important;
        }
        .main-header, .sidebar, .navbar, .topbar, .footer, .invoice-toolbar, .nexora-page-header-card,
        .main-panel > .content > .container-fluid > .d-flex > div { display: none !important; }
        .main-panel, .content, .container-fluid, .invoice-shell, #invoice {
            display: block !important;
            position: static !important;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            float: none !important;
        }
        #invoice, #invoice * {
            visibility: visible !important;
        }
        #invoice .card {
            border: 1px solid #ccc !important;
            box-shadow: none !important;
            margin-bottom: 15px !important;
        }
        #invoice .card-header {
            background: #0058A6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid invoice-shell">
            {{-- Header Card --}}
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <span class="nexora-page-header-eyebrow">Transaksi & Kasir</span>
                        <h4 class="nexora-page-header-title">Detail Invoice {{ $sale->invoice_number ?? ('#' . $sale->id) }}</h4>
                        <p class="nexora-page-header-desc">Rincian struk transaksi kasir, item belanja, dan status pembayaran pelanggan.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right d-flex flex-wrap" style="gap: 8px;">
                    @php($isCashier = strtolower((string) auth()->user()?->role?->role) === 'kasir')
                    <a href="{{ url($isCashier ? 'admin-panel/pos/sell' : 'admin-panel/sales/listing') }}" class="btn nexora-btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="button" class="btn nexora-btn-primary print">
                        <i class="fas fa-print mr-1"></i> Cetak Struk
                    </button>
                    <a href="{{ url('admin-panel/pos/pdf', $sale->id) }}" class="btn nexora-btn-secondary">
                        <i class="fas fa-file-pdf mr-1"></i> Unduh PDF
                    </a>
                    @if(!in_array($sale->status, ['cancelled', 'void']))
                    <form action="{{ url('admin-panel/sales/delete', $sale->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin membatalkan penjualan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" style="border-radius: 10px; font-weight: 600;">
                            <i class="fas fa-times mr-1"></i> Batalkan
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <div id="invoice">
            {{-- Sale Header Card --}}
            <div class="card mb-3">
                <div class="card-header">
                    <div class="invoice-brand">
                        <img src="{{ asset('admin/images/retail-point-of-sale-logo.png') }}" alt="Retail Point Of Sale" style="width: 42px; height: 42px; object-fit: contain; border-radius: 10px; background: #fff; padding: 4px;">
                        <div>
                            <div class="card-title text-white font-weight-bold" style="font-size: 16px;">{{ optional($sale->store)->name ?? 'Nexora POS' }}</div>
                            <small style="color: rgba(255,255,255,0.85);">Informasi Struk & Transaksi Penjualan</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="invoice-label">No. Invoice</div>
                            <div class="invoice-value font-weight-bold text-primary">{{ $sale->invoice_number ?? '#'.$sale->id }}</div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="invoice-label">Waktu Transaksi</div>
                            <div class="invoice-value">{{ $sale->created_at?->format('d M Y H:i') }}</div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="invoice-label">Status Penjualan</div>
                            <div>
                                <span class="badge {{ $saleStatusBadge }}" style="font-size: 12px; padding: 4px 10px;">{{ ucfirst($sale->status) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="invoice-label">Status Pembayaran</div>
                            <div>
                                <span class="badge {{ $paymentStatusBadge }}" style="font-size: 12px; padding: 4px 10px;">{{ $paymentStatus }}</span>
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="invoice-label">Toko / Cabang</div>
                            <div class="invoice-value">{{ optional($sale->store)->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="invoice-label">Kasir</div>
                            <div class="invoice-value">{{ optional($sale->user)->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="invoice-label">Pelanggan</div>
                            <div class="invoice-value">{{ optional($sale->customer)->name ?? 'Walk-in Customer' }}</div>
                            @if($sale->customer)
                            <small class="text-muted d-block">{{ $sale->customer->phone ?? '' }} {{ $sale->customer->email ? '| '.$sale->customer->email : '' }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="card mb-3">
                <div class="card-header"><div class="card-title">Daftar Item Belanja ({{ $sale->sale_items->count() }})</div></div>
                <div class="card-body p-0 table-wrap">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Produk</th>
                                <th>Varian</th>
                                <th>SKU</th>
                                <th class="text-right" style="width: 70px;">Qty</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Diskon</th>
                                <th class="text-right">Pajak</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->sale_items as $i => $it)
                                @php($itemSubtotal = (($it->price ?? 0) * ($it->quantity ?? 0)) - (($it->discount ?? 0) * ($it->quantity ?? 0)) + ($it->tax ?? 0))
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="font-weight-bold text-dark">{{ optional($it->product)->name ?? '-' }}</td>
                                    <td>{{ optional($it->product_variant)->name ?? '-' }}</td>
                                    <td><code class="text-muted">{{ optional($it->product)->sku ?? '-' }}</code></td>
                                    <td class="text-right font-weight-bold">{{ $it->quantity }}</td>
                                    <td class="text-right">Rp {{ number_format($it->price ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-right text-danger">Rp {{ number_format($it->discount ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($it->tax ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold text-primary">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</td>
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
                        <div class="card-header"><div class="card-title">Rincian Pembayaran</div></div>
                        <div class="card-body">
                            @forelse($sale->payments as $payment)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded border">
                                <div>
                                    <span class="badge badge-primary px-2 py-1">{{ optional($payment->method)->name ?? '-' }}</span>
                                    @if($payment->reference)
                                    <small class="text-muted ml-2">Ref: {{ $payment->reference }}</small>
                                    @endif
                                </div>
                                <div class="font-weight-bold text-dark" style="font-size: 15px;">Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <small class="text-muted d-block mb-3">
                                Status: <span class="badge badge-success">{{ $payment->status ?? 'Completed' }}</span> |
                                Tanggal: {{ $payment->created_at?->format('d M Y H:i') }}
                            </small>
                            @empty
                            <p class="text-muted mb-0">Belum ada pembayaran yang dicatat.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Ringkasan Total Belanja</div></div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td class="text-muted">Sub-Total</td>
                                    <td class="text-right font-weight-bold">Rp {{ number_format($sale->subtotal ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Diskon</td>
                                    <td class="text-right font-weight-bold text-danger">- Rp {{ number_format($sale->discount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Pajak (PPN)</td>
                                    <td class="text-right font-weight-bold">Rp {{ number_format($sale->tax ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr style="border-top: 2px solid #E2E8F0;">
                                    <td class="font-weight-bold text-dark pt-2" style="font-size: 15px;">TOTAL BELANJA</td>
                                    <td class="text-right pt-2"><strong class="invoice-total">Rp {{ number_format($sale->total ?? 0, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Total Diterima (Bayar)</td>
                                    <td class="text-right font-weight-bold text-success">Rp {{ number_format($sale->paid_amount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kembalian</td>
                                    <td class="text-right font-weight-bold">Rp {{ number_format($sale->change_amount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Audit --}}
            <div class="card mb-3">
                <div class="card-header"><div class="card-title">Informasi Audit Sistem</div></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="invoice-label">Dibuat Oleh</div>
                            <div class="invoice-value">{{ optional($sale->user)->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="invoice-label">Waktu Dibuat</div>
                            <div class="invoice-value">{{ $sale->created_at?->format('d M Y H:i:s') }}</div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="invoice-label">Pembaruan Terakhir</div>
                            <div class="invoice-value">{{ $sale->updated_at?->format('d M Y H:i:s') }}</div>
                        </div>
                        @if($sale->cancelled_at)
                        <div class="col-md-3 mb-2">
                            <div class="invoice-label">Waktu Dibatalkan</div>
                            <div class="invoice-value text-danger">{{ $sale->cancelled_at->format('d M Y H:i:s') }}</div>
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
