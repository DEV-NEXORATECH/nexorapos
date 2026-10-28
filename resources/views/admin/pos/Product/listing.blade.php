@extends('admin.layout.app')

@section('title', 'Katalog Produk - NexoraPOS')

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
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; KATALOG INVENTARIS</span>
                        <h3 class="nexora-page-header-title">Katalog Produk</h3>
                        <p class="nexora-page-header-desc">Kelola seluruh daftar item barang, penetapan harga jual, SKU barcode, dan kontrol stok kasir.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <a href="{{ url('admin-panel/products/recycle') }}" class="btn btn-secondary" style="border-radius: 10px; font-weight: 600; font-size: 12.5px; padding: 9px 16px; border: 1px solid #E2E8F0; color: #EF4444;" title="Tempat Sampah / Produk Terhapus">
                        <i class="fa-solid fa-trash-can mr-1"></i> Recycle Bin
                    </a>
                    <a href="{{ url('admin-panel/products/export') }}" class="btn btn-secondary" style="border-radius: 10px; font-weight: 600; font-size: 12.5px; padding: 9px 16px; border: 1px solid #E2E8F0; color: #10B981;" title="Export Produk ke Excel">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                    <button type="button" class="btn btn-secondary" id="importModal" style="border-radius: 10px; font-weight: 600; font-size: 12.5px; padding: 9px 16px; border: 1px solid #E2E8F0; color: #0284C7;" title="Import Produk dari Excel / CSV">
                        <i class="fas fa-file-import mr-1"></i> Import Excel
                    </button>
                    <!-- Hidden file input for import -->
                    <input type="file" id="fileInput" style="display:none" accept=".xlsx,.xls,.csv">

                    <a href="{{ url('admin-panel/products/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 9px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Tambah Produk Baru &rarr;
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card nexora-table-card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> DATABASE INVENTARIS</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Produk Aktif</h4>
                        <p class="card-category" style="margin-bottom: 0;">Seluruh produk barang dagangan yang siap ditransaksikan pada antarmuka kasir POS.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="productsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th>Kategori</th>
                                <th>Varian</th>
                                <th>Stok</th>
                                <th class="text-right">Harga Modal</th>
                                <th class="text-right">Harga Jual</th>
                                <th>Pajak</th>
                                <th>Lacak Stok</th>
                                <th class="text-nowrap">Perusahaan</th>
                                <th class="text-nowrap">Dibuat Oleh</th>
                                <th class="text-nowrap">Waktu Dibuat</th>
                                <th class="text-nowrap text-center" style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- AJAX loaded by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('admin/custom/product.js') }}"></script>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#productsTable')) {
            $('#productsTable').DataTable().destroy();
        }
        $('#productsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/products/listing") }}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'sku', name: 'sku' },
                { data: 'category', name: 'category.name', orderable: false, searchable: false },
                { data: 'variants', name: 'variants', searchable: false },
                { data: 'stock', name: 'stock_quantity' },
                { data: 'cost', name: 'purchase_price' },
                { data: 'price', name: 'price' },
                { data: 'tax', name: 'tax.name', orderable: false, searchable: false },
                { data: 'track_stock', name: 'track_stock' },
                { data: 'company', name: 'company.name', orderable: false, searchable: false },
                { data: 'created_by', name: 'user.name', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
