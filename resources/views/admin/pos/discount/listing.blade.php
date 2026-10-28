@extends('admin.layout.app')

@section('title', 'Daftar Diskon & Promosi - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; PROMOSI</span>
                        <h3 class="nexora-page-header-title">Diskon & Promosi</h3>
                        <p class="nexora-page-header-desc">Kelola aturan diskon persentase dan potongan nominal untuk kasir POS.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/discounts/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Tambah Diskon Baru &rarr;
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; font-weight: 500;">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('failed'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; font-weight: 500;">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('failed') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Table Card -->
            <div class="card nexora-table-card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> DATABASE PROMOSI</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Aturan Diskon</h4>
                        <p class="card-category" style="margin-bottom: 0;">Skema potongan harga aktif yang tersedia untuk kasir saat checkout.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="discountTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th>Nama Diskon</th>
                                <th>Tipe Diskon</th>
                                <th>Nilai Diskon</th>
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
        if ($.fn.DataTable.isDataTable('#discountTable')) {
            $('#discountTable').DataTable().destroy();
        }
        $('#discountTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/discounts/listing") }}',
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
                    data: 'discount_type', 
                    name: 'discount_type',
                    render: function(data) {
                        if (data === 'percentage') {
                            return '<span class="badge badge-primary px-2 py-1"><i class="fas fa-percent mr-1"></i> Persentase</span>';
                        }
                        return '<span class="badge badge-success px-2 py-1"><i class="fas fa-money-bill mr-1"></i> Nominal Tetap</span>';
                    }
                },
                { 
                    data: 'discount_value', 
                    name: 'discount_value',
                    className: 'font-weight-bold',
                    render: function(data, type, row) {
                        if (row.discount_type === 'percentage') {
                            return data + '%';
                        }
                        return 'Rp ' + Number(data || 0).toLocaleString('id-ID');
                    }
                },
                { data: 'company', name: 'company.name', orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari data diskon...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ diskon",
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
