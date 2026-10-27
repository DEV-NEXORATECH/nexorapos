@extends('admin.layout.app')

@section('title', 'Roles & Access - Hak Akses Pengguna')

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
                            OTORISASI SISTEM
                        </span>
                        <span class="hero-tag-pill">
                            <i class="fa fa-shield-alt"></i>
                            ROLES & ACCESS
                        </span>
                    </div>
                    <h2>Peran & Hak Akses Pengguna.</h2>
                    <p>Atur level otorisasi staf, pembatasan akses modul POS kasir, operasional inventaris toko, serta permission khusus per peran pengguna.</p>
                    <a href="{{ url('admin-panel/roles/add') }}" class="dashboard-hero-btn">
                        <i class="fa fa-plus-circle mr-1"></i> Tambah Role Baru &rarr;
                    </a>
                </div>
                <div class="dashboard-hero-right d-none d-lg-flex">
                    <div class="hero-stat-card-preview" style="min-width: 220px;">
                        <div class="hero-stat-badge-check">🛡️</div>
                        <small>Keamanan & Hak Akses</small>
                        <strong>Role-Based Access</strong>
                        <div class="hero-bars-sim">
                            <div class="hero-bar-col" style="height: 50%;"></div>
                            <div class="hero-bar-col" style="height: 75%;"></div>
                            <div class="hero-bar-col" style="height: 60%;"></div>
                            <div class="hero-bar-col" style="height: 90%;"></div>
                            <div class="hero-bar-col" style="height: 85%;"></div>
                            <div class="hero-bar-col" style="height: 100%; background: #10B981;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. TABLE CARD --}}
            <div class="nexora-table-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> STRUKTUR OTORISASI</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Peran & Hak Akses</h4>
                        <p class="card-category" style="margin-bottom: 0;">Seluruh role terdaftar dan jumlah staf aktif pada setiap hak akses.</p>
                    </div>
                    <a href="{{ url('admin-panel/role-permissions/listing') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px; font-weight: 600; padding: 6px 14px;">
                        <i class="fa fa-key mr-1"></i> Matriks Permission &rarr;
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table nexora-data-table" id="categoriesTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Users</th>
                                <th>Access</th>
                                <th>Status</th>
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
            ajax: '{{ url("admin-panel/roles/listing") }}',
            columns: [
                { data: 'role', name: 'role' },
                { data: 'users', name: 'users', orderable: false, searchable: false },
                { data: 'access', name: 'access', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
