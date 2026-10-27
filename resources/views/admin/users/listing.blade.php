@extends('admin.layout.app')

@section('title', 'Daftar Pengguna - Users Management')

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
                            USER & AKSES
                        </span>
                        <span class="hero-tag-pill">
                            <i class="fa fa-users"></i>
                            PENGGUNA SISTEM
                        </span>
                    </div>
                    <h2>Manajemen Pengguna & Staf.</h2>
                    <p>Kelola seluruh akun staf kasir, kepala gudang, dan administrator per cabang tenant dengan kontrol hak akses lisensi yang aman.</p>
                    <a href="{{ url('admin-panel/users/add') }}" class="dashboard-hero-btn">
                        <i class="fa fa-user-plus mr-1"></i> Tambah Pengguna Baru &rarr;
                    </a>
                </div>
                @if(auth()->user() && auth()->user()->isSuperAdmin())
                <div class="dashboard-hero-right d-none d-lg-flex">
                    <div class="hero-stat-card-preview" style="min-width: 250px;">
                        <div class="hero-stat-badge-check">🏢</div>
                        <small>Filter Tenant Perusahaan</small>
                        <select class="form-control select2 mt-2" id="superAdminCompanyFilter" style="width: 100%;">
                            <option value="">Semua Perusahaan (All)</option>
                            @php
                                $allCompanies = \App\Models\Company::orderBy('name')->get();
                            @endphp
                            @foreach($allCompanies as $c)
                                <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->company_code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
            </div>

            {{-- 2. TABLE CARD --}}
            <div class="nexora-table-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> DATABASE PENGGUNA</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Akun Pengguna Aktif</h4>
                        <p class="card-category" style="margin-bottom: 0;">Seluruh akun staf yang terdaftar pada sistem POS Nexora.</p>
                    </div>
                    @if(auth()->user() && auth()->user()->isSuperAdmin())
                    <div class="d-lg-none" style="min-width: 200px;">
                        <select class="form-control select2" id="superAdminCompanyFilterMobile" style="width: 100%;">
                            <option value="">Semua Perusahaan</option>
                            @foreach($allCompanies as $c)
                                <option value="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table nexora-data-table" id="categoriesTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Company</th>
                                <th>Store</th>
                                <th>Shift</th>
                                <th>License</th>
                                <th>Status</th>
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
        var table = $('.nexora-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/users/listing") }}',
            columns: [
                { data: 'nama', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role.name' , orderable: false, searchable: false },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'store', name: 'store.name' , orderable: false, searchable: false },
                { data: 'shift', name: 'shift' , orderable: false, searchable: false },
                { data: 'license', name: 'license' },
                { data: 'status', name: 'status' , orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#superAdminCompanyFilter').on('change', function() {
            table.column(3).search($(this).val()).draw();
        });

        $('#superAdminCompanyFilterMobile').on('change', function() {
            table.column(3).search($(this).val()).draw();
        });
    });
</script>
@endsection
