@extends('admin.layout.app')

@section('title', 'Matriks Role & Permissions - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Simple & Clean Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> USER & AKSES &bull; MATRIKS PERMISSION</span>
                        <h3 class="nexora-page-header-title">Matriks Peran & Hak Akses (Pivot)</h3>
                        <p class="nexora-page-header-desc">Ikhtisar permission aktif yang terpasang pada masing-masing peran operasional.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right d-flex align-items-center" style="gap: 10px;">
                    <a href="{{ url('admin-panel/roles/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Ke Daftar Role
                    </a>
                    <a href="{{ url('admin-panel/role-permissions/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 8px 18px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Hubungkan Hak Akses
                    </a>
                </div>
            </div>

            <!-- TABLE CARD -->
            <div class="card nexora-table-card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> PERMISSION MAPPING</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Peran Terdaftar & Rincian Izin</h4>
                        <p class="card-category" style="margin-bottom: 0;">Seluruh role berserta daftar kode izin hak akses yang telah dialokasikan.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="rolePermissionsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#ID</th>
                                <th style="width: 140px;">Nama Peran</th>
                                <th>Daftar Hak Akses (Permissions)</th>
                                <th class="text-nowrap" style="width: 140px;">Company</th>
                                <th class="text-nowrap" style="width: 120px;">Dibuat Oleh</th>
                                <th class="text-nowrap" style="width: 130px;">Tanggal Dibuat</th>
                                <th class="text-nowrap text-center" style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td><span class="badge badge-light" style="font-weight: 700; color: #64748B;">#{{ $role->id }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center" style="gap: 8px;">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(0, 88, 166, 0.1); color: #0058A6; font-size: 11px;">
                                                <i class="fas fa-shield"></i>
                                            </div>
                                            <strong style="color: #0F1F3D; font-size: 13.5px;">{{ $role->role }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 4px; max-width: 600px;">
                                            @forelse($role->permissions as $perm)
                                                <span class="badge" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #DBEAFE; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 500;">
                                                    {{ $perm->name }}
                                                </span>
                                            @empty
                                                <span class="text-muted" style="font-size: 12px; font-style: italic;">Tidak ada hak akses khusus</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="badge badge-pill badge-light" style="font-weight: 600; color: #475569; padding: 5px 10px; border: 1px solid #E2E8F0;">
                                            {{ $role->company?->name ?? 'Platform Global' }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap" style="font-size: 13px; color: #475569;">
                                        {{ $role->user?->name ?? $role->creator?->name ?? 'System' }}
                                    </td>
                                    <td class="text-nowrap" style="font-size: 12.5px; color: #64748B;">
                                        {{ $role->created_at ? $role->created_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="{{ url('admin-panel/role-permissions/edit', $role->id) }}" class="nexora-action-btn is-warning" title="Edit Matriks">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('admin-panel/role-permissions/delete', $role->id) }}" class="nexora-action-btn is-danger delete-category" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
        if ($.fn.DataTable.isDataTable('#rolePermissionsTable')) {
            $('#rolePermissionsTable').DataTable().destroy();
        }
        $('#rolePermissionsTable').DataTable({
            pageLength: 10,
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: "Cari role / hak akses...",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "&larr;",
                    next: "&rarr;"
                }
            }
        });
    });
</script>
<script src="{{ asset('admin/custom/js/category.js') }}"></script>
@endsection
