@extends('admin.layout.app')

@section('title', 'Daftar Toko & Cabang - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; OUTLET & CABANG</span>
                        <h3 class="nexora-page-header-title">Daftar Toko & Cabang</h3>
                        <p class="nexora-page-header-desc">Kelola seluruh lokasi toko fisik, outlet kasir, dan penempatan cabang operasional.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/stores/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Tambah Toko Baru &rarr;
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card nexora-table-card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> DATABASE TOKO</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Outlet Aktif</h4>
                        <p class="card-category" style="margin-bottom: 0;">Data cabang toko yang telah terdaftar dan terintegrasi pada sistem POS.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="storesTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 110px;">Kode Toko</th>
                                <th>Nama Toko / Cabang</th>
                                <th>Tanggal Dibuat</th>
                                <th class="text-nowrap">Perusahaan</th>
                                <th class="text-nowrap">Dibuat Oleh</th>
                                <th class="text-nowrap">Waktu Dibuat</th>
                                <th class="text-nowrap text-center action-col" style="min-width: 130px; width: 130px;">Aksi</th>
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
        if ($.fn.DataTable.isDataTable('#storesTable')) {
            $('#storesTable').DataTable().destroy();
        }
        $('#storesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/stores/listing") }}',
            columns: [
                { data: 'code', name: 'code' },
                { data: 'name', name: 'name' },
                { data: 'created_at_short', name: 'created_at', searchable: false },
                { data: 'company', name: 'company.name', orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center text-nowrap action-col' }
            ]
        });
    });
</script>
@endsection
