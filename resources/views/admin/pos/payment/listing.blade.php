@extends('admin.layout.app')

@section('title', 'Catatan Pembayaran')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            {{-- Header Card --}}
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div>
                        <span class="nexora-page-header-eyebrow">Transaksi & Kasir</span>
                        <h4 class="nexora-page-header-title">Catatan Pembayaran</h4>
                        <p class="nexora-page-header-desc">Kelola dan pantau riwayat penerimaan kas dan transaksi pembayaran pelanggan.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/payments/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 9px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Tambah Pembayaran
                    </a>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('failed'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('failed') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Table Card --}}
            <div class="nexora-table-card">
                <div class="table-responsive">
                    <table class="table nexora-data-table" id="paymentsTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Invoice / Penjualan</th>
                                <th>Status Sale</th>
                                <th>Metode Bayar</th>
                                <th>Jumlah Bayar</th>
                                <th>No. Referensi</th>
                                <th>Status</th>
                                <th>Perusahaan</th>
                                <th>Waktu Transaksi</th>
                                <th class="text-center" style="width: 90px;">Aksi</th>
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
        if ($.fn.DataTable.isDataTable('#paymentsTable')) {
            $('#paymentsTable').DataTable().destroy();
        }

        $('#paymentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/payments/listing") }}',
            order: [[0, 'desc']],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'sale_ref', name: 'sale.invoice_number' },
                { data: 'sale_status', name: 'sale.status', orderable: false, searchable: false },
                { data: 'method', name: 'method.name' },
                { data: 'amount', name: 'amount' },
                { data: 'reference', name: 'reference' },
                { data: 'status', name: 'status' },
                { data: 'company', name: 'company.name', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari pembayaran...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "<i class='fas fa-chevron-left'></i>",
                    next: "<i class='fas fa-chevron-right'></i>"
                }
            }
        });
    });
</script>
@endsection
