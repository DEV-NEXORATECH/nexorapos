@extends('admin.layout.app')

@section('title', 'Daftar Supplier - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; DISTRIBUTOR</span>
                        <h3 class="nexora-page-header-title">Daftar Supplier & Distributor</h3>
                        <p class="nexora-page-header-desc">Kelola data vendor, kontak supplier, dan rekanan pengadaan barang toko.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/supplier/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Tambah Supplier Baru &rarr;
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card nexora-table-card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> DATABASE SUPPLIER</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Rekanan Vendor</h4>
                        <p class="card-category" style="margin-bottom: 0;">Kontak dan alamat supplier untuk pemesanan stok barang.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="supplierTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th>Nama Supplier</th>
                                <th>Email</th>
                                <th>Alamat</th>
                                <th>No. Telepon</th>
                                <th class="text-nowrap">Perusahaan</th>
                                <th class="text-nowrap">Dibuat Oleh</th>
                                <th class="text-nowrap">Waktu Dibuat</th>
                                <th class="text-nowrap text-center" style="width: 120px;">Aksi</th>
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
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#supplierTable')) {
            $('#supplierTable').DataTable().destroy();
        }
        $('#supplierTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/supplier/listing") }}',
            columns: [
                { data: 'id', name: 'id' },
                { 
                    data: 'name', 
                    name: 'name',
                    render: function(data) {
                        return '<span class="font-weight-bold text-dark">' + (data || '-') + '</span>';
                    }
                },
                { data: 'email', name: 'email', defaultContent: '-' },
                { data: 'address', name: 'address', defaultContent: '-' },
                { data: 'phone', name: 'phone', defaultContent: '-' },
                { data: 'company', name: 'company.name', orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari data supplier...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ supplier",
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
