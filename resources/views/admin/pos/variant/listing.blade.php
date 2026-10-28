@extends('admin.layout.app')

@section('title', 'Varian Produk - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; VARIAN ITEM</span>
                        <h3 class="nexora-page-header-title">Varian Produk</h3>
                        <p class="nexora-page-header-desc">Kelola opsi ukuran, warna, atau varian rasa untuk item barang induk pada katalog.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/variants/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Tambah Varian Baru &rarr;
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card nexora-table-card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> DATABASE VARIAN</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Varian Terdaftar</h4>
                        <p class="card-category" style="margin-bottom: 0;">Seluruh variasi produk yang terhubung dengan produk induk dalam sistem.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="variantsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#ID</th>
                                <th>Produk Induk</th>
                                <th>Nama Varian</th>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th class="text-right">Harga Jual</th>
                                <th class="text-right">Harga Modal</th>
                                <th class="text-nowrap">Perusahaan</th>
                                <th class="text-nowrap">Dibuat Oleh</th>
                                <th class="text-nowrap">Waktu Dibuat</th>
                                <th class="text-nowrap text-center action-col" style="min-width: 100px; width: 100px;">Aksi</th>
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
<script src="{{ asset('admin/custom/js/category.js') }}"></script>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#variantsTable')) {
            $('#variantsTable').DataTable().destroy();
        }
        $('#variantsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/variants/listing") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'product_name', name: 'product.name' },
                { data: 'name', name: 'name' },
                { data: 'sku', name: 'sku' },
                { data: 'barcode', name: 'barcode' },
                { data: 'price', name: 'price', className: 'text-right' },
                { data: 'cost', name: 'cost', className: 'text-right' },
                { data: 'company', name: 'company.name', orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center text-nowrap action-col' }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari data varian...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ varian",
                infoEmpty: "Menampilkan 0 data",
                zeroRecords: "Tidak ditemukan data yang cocok",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            }
        });
    });
</script>
@endsection
