@extends('admin.layout.app')

@section('title', 'Metode Pembayaran - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; TRANSAKSI</span>
                        <h3 class="nexora-page-header-title">Metode Pembayaran (Payment Methods)</h3>
                        <p class="nexora-page-header-desc">Kelola opsi pembayaran kasir (Tunai, QRIS, Transfer Bank, Debit/Kredit, EDC).</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/payment-methods/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Tambah Metode Pembayaran &rarr;
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card nexora-table-card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> DATABASE PEMBAYARAN</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Channel Pembayaran</h4>
                        <p class="card-category" style="margin-bottom: 0;">Pilihan metode transaksi yang aktif pada sistem kasir POS.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="methodsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th>Nama Metode</th>
                                <th class="text-nowrap">Perusahaan</th>
                                <th class="text-nowrap">Dibuat Oleh</th>
                                <th class="text-nowrap">Waktu Dibuat</th>
                                <th class="text-nowrap text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($methods as $method)
                                <tr>
                                    <td>{{ $method->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center" style="gap: 10px;">
                                            <span class="badge badge-light border p-2 text-primary" style="border-radius: 8px;">
                                                <i class="fas fa-wallet"></i>
                                            </span>
                                            <span class="font-weight-bold text-dark">{{ $method->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-nowrap">{{ $method->company?->name ?? 'System' }}</td>
                                    <td class="text-nowrap">{{ $method->user?->name ?? $method->creator?->name ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $method->created_at ? $method->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                                            <a href="{{ url('admin-panel/payment-methods/edit', $method->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="{{ url('admin-panel/payment-methods/delete', $method->id) }}" class="nexora-action-btn is-danger delete-item" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus metode pembayaran ini?')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
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
        if ($.fn.DataTable.isDataTable('#methodsTable')) {
            $('#methodsTable').DataTable().destroy();
        }
        $('#methodsTable').DataTable({
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari metode pembayaran...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ metode",
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
