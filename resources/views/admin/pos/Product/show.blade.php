@extends('admin.layout.app')

@section('title', 'Detail Produk — ' . $product->name)

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; DETAIL PRODUK</span>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <h3 class="nexora-page-header-title mb-0">{{ $product->name }}</h3>
                            <span class="badge badge-light px-2 py-1" style="font-weight: 700; color: #0058A6; font-size: 12px; background: #EBF4FC; border: 1px solid #BFDBFE;">
                                SKU: {{ $product->sku ?? '-' }}
                            </span>
                        </div>
                        <p class="nexora-page-header-desc">Rincian parameter harga, kategori, ketersediaan varian, dan mutasi persediaan barang.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right d-flex align-items-center" style="gap: 10px;">
                    <a href="{{ url('admin-panel/products/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Katalog
                    </a>
                    <a href="{{ url('admin-panel/products/edit', $product->id) }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-edit mr-1"></i> Edit Data Produk
                    </a>
                </div>
            </div>

            <div class="row">
                {{-- Product Information & Pricing --}}
                <div class="col-lg-5 mb-4">
                    <div class="card nexora-form-card mb-4">
                        <div class="nexora-form-card-head">
                            <div class="nexora-form-card-icon is-navy">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div>
                                <span class="nexora-form-card-eyebrow">SPESIFIKASI BARANG</span>
                                <h4 class="nexora-form-card-title">Informasi & Barcode</h4>
                            </div>
                        </div>
                        <div class="nexora-form-card-body p-0">
                            <table class="table mb-0" style="font-size: 13px;">
                                <tbody>
                                    <tr>
                                        <td style="width: 40%; color: #64748B; padding: 12px 20px;">Nama Produk</td>
                                        <td style="font-weight: 700; color: #0F1F3D; padding: 12px 20px;">{{ $product->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 12px 20px;">Kode SKU</td>
                                        <td style="font-weight: 600; padding: 12px 20px;">{{ $product->sku ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 12px 20px;">Barcode EAN</td>
                                        <td style="padding: 12px 20px;">{{ $product->barcode ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 12px 20px;">Kategori</td>
                                        <td style="padding: 12px 20px;">
                                            <span class="badge badge-light" style="font-weight: 600; color: #475569; background: #F1F5F9;">
                                                {{ optional($product->category)->name ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 12px 20px;">Tarif Pajak</td>
                                        <td style="padding: 12px 20px;">{{ $product->tax ? $product->tax->name . ' (' . $product->tax->rate . '%)' : '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card nexora-form-card mb-4">
                        <div class="nexora-form-card-head">
                            <div class="nexora-form-card-icon is-emerald">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div>
                                <span class="nexora-form-card-eyebrow">MARGIN & STOK</span>
                                <h4 class="nexora-form-card-title">Harga & Persediaan</h4>
                            </div>
                        </div>
                        <div class="nexora-form-card-body p-0">
                            <table class="table mb-0" style="font-size: 13px;">
                                <tbody>
                                    <tr>
                                        <td style="width: 40%; color: #64748B; padding: 12px 20px;">Harga Modal</td>
                                        <td style="padding: 12px 20px;">Rp {{ number_format($product->cost, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 12px 20px;">Harga Jual Kasir</td>
                                        <td style="font-weight: 700; color: #10B981; padding: 12px 20px;">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 12px 20px;">Estimasi Margin</td>
                                        <td style="font-weight: 700; color: #0058A6; padding: 12px 20px;">
                                            @php $margin = $product->cost > 0 ? (($product->price - $product->cost) / $product->cost) * 100 : 0; @endphp
                                            {{ number_format($margin, 1) }}%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 12px 20px;">Lacak Stok Otomatis</td>
                                        <td style="padding: 12px 20px;">{{ $product->track_stock ? 'Ya (Aktif)' : 'Tidak (Jasa / Unlimited)' }}</td>
                                    </tr>
                                    @if($product->track_stock)
                                    <tr>
                                        <td style="color: #64748B; padding: 12px 20px;">Sisa Stok Saat Ini</td>
                                        <td style="padding: 12px 20px;">
                                            @php
                                                $isLow = $product->stock_quantity <= ($product->minimum_stock ?? 0);
                                                $stkBg = $isLow ? 'background: #FDE8E8; color: #9B1C1C; border: 1px solid #FBD5D5;' : 'background: #DEF7EC; color: #03543F; border: 1px solid #BCF0DA;';
                                            @endphp
                                            <span class="badge" style="border-radius: 6px; font-weight: 700; font-size: 12px; padding: 4px 10px; {{ $stkBg }}">
                                                {{ number_format($product->stock_quantity) }} unit
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Variants & Stock History --}}
                <div class="col-lg-7 mb-4">
                    {{-- Product Variants Table --}}
                    <div class="card nexora-table-card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #E6EBF3; padding: 18px 24px;">
                            <div>
                                <span class="eyebrow-tag"><span class="dot"></span> VARIASI ITEM</span>
                                <h4 class="card-title mb-0" style="font-size: 16px; font-weight: 700; color: #0F1F3D;">Daftar Varian Produk ({{ $product->product_variants->count() }})</h4>
                            </div>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table mb-0" style="font-size: 13px;">
                                <thead>
                                    <tr style="background: #F8FAFC;">
                                        <th style="padding: 12px 18px;">Nama Varian</th>
                                        <th>SKU</th>
                                        <th>Barcode</th>
                                        <th class="text-right">Harga Modal</th>
                                        <th class="text-right">Harga Jual</th>
                                        <th class="text-center">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($product->product_variants as $variant)
                                    <tr>
                                        <td style="padding: 12px 18px; font-weight: 700; color: #0F1F3D;">{{ $variant->name }}</td>
                                        <td>{{ $variant->sku ?? '-' }}</td>
                                        <td>{{ $variant->barcode ?? '-' }}</td>
                                        <td class="text-right">Rp {{ number_format($variant->cost, 0, ',', '.') }}</td>
                                        <td class="text-right" style="font-weight: 700; color: #10B981;">Rp {{ number_format($variant->price, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-light" style="font-weight: 700; color: #0058A6; background: #EBF4FC;">
                                                {{ $variant->stock_quantity ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4" style="color: #94A3B8;">Produk ini merupakan item tunggal (tidak memiliki varian ukuran/rasa).</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Stock Movements --}}
                    @if($product->track_stock && isset($stockMovements))
                    <div class="card nexora-table-card">
                        <div class="card-header" style="border-bottom: 1px solid #E6EBF3; padding: 18px 24px;">
                            <span class="eyebrow-tag"><span class="dot"></span> LOG LOGISTIK</span>
                            <h4 class="card-title mb-0" style="font-size: 16px; font-weight: 700; color: #0F1F3D;">Riwayat Mutasi Stok Terbaru</h4>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table mb-0" style="font-size: 13px;">
                                <thead>
                                    <tr style="background: #F8FAFC;">
                                        <th style="padding: 12px 18px;">Waktu</th>
                                        <th>Tipe Mutasi</th>
                                        <th class="text-center">Jumlah</th>
                                        <th>Keterangan / Ref</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockMovements as $sm)
                                    <tr>
                                        <td style="padding: 12px 18px; color: #64748B;">{{ $sm->created_at?->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @php
                                                $mBadge = $sm->type === 'in' ? 'background: #DEF7EC; color: #03543F; border: 1px solid #BCF0DA;' : 'background: #FDE8E8; color: #9B1C1C; border: 1px solid #FBD5D5;';
                                            @endphp
                                            <span class="badge" style="border-radius: 6px; font-weight: 700; font-size: 11px; padding: 4px 8px; {{ $mBadge }}">
                                                {{ strtoupper($sm->type) }}
                                            </span>
                                        </td>
                                        <td class="text-center" style="font-weight: 700; color: #0F1F3D;">{{ $sm->quantity }}</td>
                                        <td style="color: #64748B;">{{ $sm->reference ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4" style="color: #94A3B8;">Belum ada log mutasi persediaan untuk barang ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
