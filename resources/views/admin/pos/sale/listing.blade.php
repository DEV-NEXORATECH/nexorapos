@extends('admin.layout.app')

@section('title', 'Riwayat Penjualan')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            {{-- Header Card --}}
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div>
                        <span class="nexora-page-header-eyebrow">Transaksi & Kasir</span>
                        <h4 class="nexora-page-header-title">Riwayat Penjualan</h4>
                        <p class="nexora-page-header-desc">Daftar transaksi kasir, cetak struk belanja, dan status pembayaran pelanggan.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right d-flex flex-wrap" style="gap: 10px;">
                    <a href="{{ url('admin-panel/sales/reconciliation') }}" class="btn nexora-btn-secondary">
                        <i class="fas fa-balance-scale mr-1"></i> Rekonsiliasi
                    </a>
                    <a href="{{ url('admin-panel/pos/sell') }}" class="btn nexora-btn-primary">
                        <i class="fas fa-cash-register mr-1"></i> Buka Kasir POS
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
                    <table class="table nexora-data-table" id="salesTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No. Invoice</th>
                                <th>Waktu Transaksi</th>
                                <th>Store</th>
                                <th>Pelanggan</th>
                                <th>Kasir</th>
                                <th>Items</th>
                                <th>Grand Total</th>
                                <th>Metode Bayar</th>
                                <th>Status Bayar</th>
                                <th>Status Sale</th>
                                <th>Perusahaan</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
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
        if ($.fn.DataTable.isDataTable('#salesTable')) {
            $('#salesTable').DataTable().destroy();
        }

        $('#salesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/sales/listing") }}',
            order: [[0, 'desc']],
            columns: [
                { data: 'invoice', name: 'invoice_number' },
                { data: 'date', name: 'created_at' },
                { data: 'store', name: 'store.name', orderable: false, searchable: false },
                { data: 'customer', name: 'customer.name', orderable: false, searchable: false },
                { data: 'cashier', name: 'user.name', orderable: false, searchable: false },
                { data: 'items', name: 'items', orderable: false, searchable: false },
                { data: 'grand_total', name: 'total' },
                { data: 'payment_method', name: 'payment_method', orderable: false, searchable: false },
                { data: 'payment_status', name: 'payment_status' },
                { data: 'sale_status', name: 'status' },
                { data: 'company', name: 'company.name', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari nomor invoice, kasir...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi",
                paginate: {
                    previous: "<i class='fas fa-chevron-left'></i>",
                    next: "<i class='fas fa-chevron-right'></i>"
                }
            }
        });
    });
</script>
@endsection
