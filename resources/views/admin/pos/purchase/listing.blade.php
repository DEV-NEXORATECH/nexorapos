@extends('admin.layout.app')

@section('title', 'Daftar Pembelian Barang - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-cart-shopping"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> TRANSAKSI & KASIR &bull; PENGADAAN</span>
                        <h3 class="nexora-page-header-title">Pembelian Barang</h3>
                        <p class="nexora-page-header-desc">Kelola pencatatan faktur pembelian, pengadaan stok dari supplier, dan penerimaan inventaris.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/purchase/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Input Pembelian Baru &rarr;
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
                        <span class="eyebrow-tag"><span class="dot"></span> DATABASE PEMBELIAN</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Riwayat Faktur Pengadaan</h4>
                        <p class="card-category" style="margin-bottom: 0;">Seluruh dokumen purchase order dan invoice pengadaan barang ke supplier.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="purchasesTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>No. Pembelian</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>No. Referensi</th>
                                <th class="text-right">Total Nominal</th>
                                <th class="text-center">Status</th>
                                <th class="text-nowrap">Perusahaan</th>
                                <th class="text-nowrap">Waktu Dibuat</th>
                                <th class="text-nowrap text-center" style="width: 130px;">Aksi</th>
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
        if ($.fn.DataTable.isDataTable('#purchasesTable')) {
            $('#purchasesTable').DataTable().destroy();
        }
        $('#purchasesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/purchase/listing") }}',
            columns: [
                { 
                    data: 'purchase_number', 
                    name: 'purchase_number',
                    render: function(data) {
                        return '<span class="font-weight-bold text-dark">' + (data || '-') + '</span>';
                    }
                },
                { data: 'date', name: 'date' },
                { data: 'supplier', name: 'supplier.name', orderable: false, searchable: false },
                { data: 'reference', name: 'reference_number' },
                { 
                    data: 'total_amount', 
                    name: 'total_amount', 
                    className: 'text-right font-weight-bold text-dark' 
                },
                { 
                    data: 'status', 
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data) {
                        const st = (data || '').toUpperCase();
                        if (st === 'RECEIVED') {
                            return '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> RECEIVED</span>';
                        } else if (st === 'PENDING') {
                            return '<span class="badge badge-warning px-2 py-1 text-dark"><i class="fas fa-clock mr-1"></i> PENDING</span>';
                        } else if (st === 'CANCELLED') {
                            return '<span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> CANCELLED</span>';
                        }
                        return '<span class="badge badge-secondary px-2 py-1">' + (data || '-') + '</span>';
                    }
                },
                { data: 'company', name: 'company.name', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari data pembelian...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ faktur",
                infoEmpty: "Menampilkan 0 data",
                zeroRecords: "Tidak ditemukan faktur pembelian yang cocok",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            }
        });
    });
</script>
@endsection
