@extends('admin.layout.app')

@section('title', 'Recycle Bin Produk - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon" style="background: rgba(239, 68, 68, 0.1); color: #EF4444;">
                        <i class="fa-solid fa-trash-can"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow" style="color: #EF4444;"><span class="dot" style="background: #EF4444;"></span> MASTER DATA POS &bull; RECYCLE BIN</span>
                        <h3 class="nexora-page-header-title">Tempat Sampah Produk</h3>
                        <p class="nexora-page-header-desc">Daftar item produk yang diarsipkan/dihapus sementara. Anda dapat memulihkan kembali ke katalog atau menghapusnya secara permanen.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/products/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Katalog
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card nexora-table-card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> ARSIP PRODUK</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Produk Terhapus</h4>
                        <p class="card-category" style="margin-bottom: 0;">Total {{ $deletedProducts->count() }} produk berada dalam arsip tempat sampah.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="deletedProductsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#ID</th>
                                <th>Nama Produk</th>
                                <th>Tarif Pajak</th>
                                <th class="text-right">Harga Jual</th>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th class="text-right">Harga Modal</th>
                                <th class="text-center">Lacak Stok</th>
                                <th class="text-nowrap text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($deletedProducts as $product)
                            <tr>
                                <td><span class="badge badge-light" style="font-weight: 700; color: #64748B;">#{{ $product->id }}</span></td>
                                <td style="font-weight: 700; color: #0F1F3D;">{{ $product->name }}</td>
                                <td>{{ $product->tax ? $product->tax->rate . '%' : '-' }}</td>
                                <td class="text-right" style="font-weight: 700; color: #10B981;">
                                    @php $totalPrice = $product->tax ? ($product->price * $product->tax->rate / 100) + $product->price : $product->price; @endphp
                                    Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                </td>
                                <td>{{ $product->sku ?? '-' }}</td>
                                <td>{{ $product->barcode ?? '-' }}</td>
                                <td class="text-right">Rp {{ number_format($product->cost, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge badge-light" style="font-size: 11px;">{{ $product->track_stock ? 'Ya' : 'Tidak' }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="table-actions justify-content-center">
                                        <a href="{{ url('admin-panel/products/restore', $product->id) }}" class="btn-action btn-action-primary restore-category" title="Pulihkan Produk ke Katalog">
                                            <i class="fa-solid fa-trash-can-arrow-up"></i>
                                        </a>
                                        <a href="{{ url('admin-panel/products/force-delete', $product->id) }}" class="btn-action btn-action-danger force-delete" title="Hapus Permanen dari Database">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4" style="color: #94A3B8;">Tempat sampah kosong. Tidak ada produk yang sedang diarsipkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
