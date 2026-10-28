@extends('admin.layout.app')

@section('title', 'Detail Faktur Pembelian — ' . ($purchase->purchase_number ?? 'PUR-'.$purchase->id))

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4 d-print-none">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> TRANSAKSI & KASIR &bull; DETAIL PENGADAAN</span>
                        <h3 class="nexora-page-header-title">Faktur Pembelian: {{ $purchase->purchase_number ?? 'PUR-'.$purchase->id }}</h3>
                        <p class="nexora-page-header-desc">Rincian invoice barang masuk, informasi vendor supplier, dan arsip audit transaksi.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right" style="display: flex; gap: 8px;">
                    <a href="{{ url('admin-panel/purchase/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    @if(strtoupper($purchase->status) !== 'RECEIVED' && strtoupper($purchase->status) !== 'CANCELLED')
                        <a href="{{ url('admin-panel/purchase/edit', $purchase->id) }}" class="btn btn-warning" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 18px;">
                            <i class="fas fa-edit mr-1"></i> Edit Faktur
                        </a>
                    @endif
                    <button type="button" class="btn btn-info print-btn" onclick="window.print()" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 18px; background: #0284C7; border: none;">
                        <i class="fas fa-print mr-1"></i> Cetak Dokumen
                    </button>
                </div>
            </div>

            <div id="printableArea">
                <!-- Header Info Card -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">DOKUMEN FAKTUR</span>
                            <h4 class="nexora-form-card-title">Informasi Pembelian</h4>
                            <p class="nexora-form-card-subtitle">Nomor faktur, waktu pencatatan, dan status penerimaan inventaris.</p>
                        </div>
                    </div>
                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <small class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">No. Pembelian</small>
                                <span class="font-weight-bold" style="font-size: 15px; color: #0F1F3D;">{{ $purchase->purchase_number ?? 'PUR-'.$purchase->id }}</span>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <small class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Tanggal Faktur</small>
                                <span class="font-weight-bold" style="font-size: 14px; color: #334155;">{{ $purchase->created_at?->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <small class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Status Penerimaan</small>
                                <div>
                                    @php
                                        $st = strtoupper($purchase->status ?? 'PENDING');
                                        $badgeClass = match($st) {
                                            'RECEIVED' => 'badge-success',
                                            'PENDING' => 'badge-warning text-dark',
                                            'CANCELLED' => 'badge-danger',
                                            default => 'badge-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-2 py-1" style="font-size: 12px; font-weight: 700;">
                                        {{ $st }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <small class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">No. Referensi / Surat Jalan</small>
                                <span class="font-weight-bold" style="font-size: 14px; color: #334155;">{{ $purchase->reference_number ?: '-' }}</span>
                            </div>
                        </div>

                        <hr style="border-top: 1px solid #E6EBF3; margin: 12px 0 20px 0;">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <small class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Supplier / Vendor</small>
                                <div class="font-weight-bold" style="font-size: 16px; color: #0058A6;">{{ optional($purchase->supplier)->name ?? '-' }}</div>
                                @if($purchase->supplier)
                                    <div class="text-muted small mt-1">
                                        <i class="fas fa-phone mr-1"></i> {{ $purchase->supplier->phone ?: '-' }} &bull;
                                        <i class="fas fa-envelope mr-1"></i> {{ $purchase->supplier->email ?: '-' }}
                                    </div>
                                    @if($purchase->supplier->address)
                                        <div class="text-muted small mt-1"><i class="fas fa-map-marker-alt mr-1"></i> {{ $purchase->supplier->address }}</div>
                                    @endif
                                @endif
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <small class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Toko / Outlet</small>
                                <div class="font-weight-bold" style="font-size: 14px; color: #0F1F3D;">{{ optional($purchase->store)->name ?: 'Semua Outlet' }}</div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <small class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Perusahaan / Tenant</small>
                                <div class="font-weight-bold" style="font-size: 14px; color: #0F1F3D;">{{ optional($purchase->company)->name ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table Card -->
                <div class="card nexora-table-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center" style="padding: 16px 22px; border-bottom: 1px solid #E6EBF3;">
                        <div>
                            <span class="eyebrow-tag"><span class="dot"></span> RINCIAN BARANG</span>
                            <h4 class="card-title mb-0" style="font-size: 16px; font-weight: 700; color: #0F1F3D;">
                                Item Pembelian ({{ $purchase->purchase_items ? $purchase->purchase_items->count() : 0 }} Item)
                            </h4>
                        </div>
                    </div>
                    <div class="table-responsive p-0">
                        <table class="table nexora-data-table mb-0" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nama Produk</th>
                                    <th>SKU Produk</th>
                                    <th class="text-center" style="width: 120px;">Qty</th>
                                    <th class="text-right" style="width: 180px;">Biaya Satuan</th>
                                    <th class="text-right" style="width: 200px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchase->purchase_items as $index => $item)
                                    @php
                                        $cost = $item->cost ?? $item->unit_cost ?? 0;
                                        $qty = $item->quantity ?? 0;
                                        $subtotal = $qty * $cost;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="font-weight-bold text-dark">{{ optional($item->product)->name ?? '-' }}</td>
                                        <td>{{ optional($item->product)->sku ?: '-' }}</td>
                                        <td class="text-center font-weight-bold">{{ $qty }}</td>
                                        <td class="text-right">Rp {{ number_format($cost, 0, ',', '.') }}</td>
                                        <td class="text-right font-weight-bold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada rincian item dalam faktur ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr style="background: #F8FAFC;">
                                    <th colspan="5" class="text-right font-weight-bold" style="font-size: 15px; color: #334155;">
                                        TOTAL KESELURUHAN:
                                    </th>
                                    <th class="text-right font-weight-bold" style="font-size: 18px; color: #0058A6;">
                                        Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div> {{-- end #printableArea --}}

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printableArea, #printableArea * {
            visibility: visible;
        }
        #printableArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .d-print-none {
            display: none !important;
        }
    }
</style>
@endpush
