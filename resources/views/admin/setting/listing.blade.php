@extends('admin.layout.app')

@section('title', 'System Settings - Pengaturan Sistem')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            {{-- 1. HERO BANNER (JOB-FINANCE STYLE) --}}
            <div class="dashboard-hero-banner">
                <div class="dashboard-hero-left">
                    <div class="dashboard-hero-badges">
                        <span class="hero-tag-pill">
                            <i class="fa fa-circle" style="font-size: 6px; color: #10B981;"></i>
                            PENGATURAN SISTEM
                        </span>
                        <span class="hero-tag-pill">
                            <i class="fa fa-sliders-h"></i>
                            SYSTEM SETTINGS
                        </span>
                    </div>
                    <h2>Pengaturan Parameter Sistem.</h2>
                    <p>Konfigurasi variabel global POS, format nota struk kasir, prefix invoice penjualan, mata uang, dan zona waktu operasional toko.</p>
                    <a href="{{ url('admin-panel/settings/add') }}" class="dashboard-hero-btn">
                        <i class="fa fa-plus-circle mr-1"></i> Tambah Setting Baru &rarr;
                    </a>
                </div>
                <div class="dashboard-hero-right d-none d-lg-flex">
                    <div class="hero-stat-card-preview" style="min-width: 220px;">
                        <div class="hero-stat-badge-check">⚙️</div>
                        <small>Status Konfigurasi</small>
                        <strong>Global Parameter</strong>
                        <div class="hero-bars-sim">
                            <div class="hero-bar-col" style="height: 40%;"></div>
                            <div class="hero-bar-col" style="height: 60%;"></div>
                            <div class="hero-bar-col" style="height: 75%;"></div>
                            <div class="hero-bar-col" style="height: 65%;"></div>
                            <div class="hero-bar-col" style="height: 90%;"></div>
                            <div class="hero-bar-col" style="height: 100%; background: #10B981;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. TABLE CARD --}}
            <div class="nexora-table-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> VARIABEL SISTEM</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Variabel & Pengaturan Aplikasi</h4>
                        <p class="card-category" style="margin-bottom: 0;">Parameter konfigurasi aktif yang mengatur perilaku sistem POS dan toko.</p>
                    </div>
                    <a href="{{ url('admin-panel/settings') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px; font-weight: 600; padding: 6px 14px;">
                        <i class="fa fa-store mr-1"></i> Pengaturan Toko &rarr;
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table nexora-data-table" id="categoriesTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Key</th>
                                <th>Value</th>
                                <th class="text-nowrap">Company</th>
                                <th class="text-nowrap">Created By</th>
                                <th class="text-nowrap">Created At</th>
                                <th class="text-nowrap" style="width: 85px;">Action</th>
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
        if ($.fn.DataTable.isDataTable('.nexora-data-table')) {
            $('.nexora-data-table').DataTable().destroy();
        }
        $('.nexora-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/settings/listing") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'key', name: 'key' },
                { data: 'value', name: 'value' },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
