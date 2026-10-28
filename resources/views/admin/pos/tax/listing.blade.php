@extends('admin.layout.app')

@section('title', 'Daftar Pajak / Tax - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; PERPAJAKAN</span>
                        <h3 class="nexora-page-header-title">Pengaturan Pajak (Tax)</h3>
                        <p class="nexora-page-header-desc">Kelola jenis dan persentase tarif pajak yang berlaku pada transaksi POS.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/tax/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Tambah Pajak Baru &rarr;
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card nexora-table-card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> DATABASE PAJAK</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Tarif Pajak</h4>
                        <p class="card-category" style="margin-bottom: 0;">Daftar skema pajak yang aktif untuk transaksi kasir.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="taxTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th>Nama Pajak</th>
                                <th>Tarif Pajak (%)</th>
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
        if ($.fn.DataTable.isDataTable('#taxTable')) {
            $('#taxTable').DataTable().destroy();
        }
        $('#taxTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/tax/listing") }}',
            columns: [
                { data: 'id', name: 'id' },
                { 
                    data: 'name', 
                    name: 'name',
                    render: function(data) {
                        return '<span class="font-weight-bold text-dark">' + (data || '-') + '</span>';
                    }
                },
                { 
                    data: 'rate', 
                    name: 'rate',
                    render: function(data) {
                        return '<span class="badge badge-info font-weight-bold px-2 py-1">' + data + '%</span>';
                    }
                },
                { data: 'company', name: 'company.name', orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari data pajak...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ pajak",
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
