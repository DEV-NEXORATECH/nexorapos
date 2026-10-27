@extends('admin.layout.app')

@section('title', 'Perbarui Hak Akses Peran - ' . $role->role)

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
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> USER & AKSES &bull; EDIT ROLE PERMISSIONS</span>
                        <h3 class="nexora-page-header-title">Perbarui Hak Akses: {{ $role->role }}</h3>
                        <p class="nexora-page-header-desc">Sesuaikan alokasi modul izin yang dapat diakses oleh peran ini.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/role-permissions/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Matriks
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/role-permissions/update' , $role->id) }}" method="POST">
                @csrf
                
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">PEMETAAN IZIN</span>
                            <h4 class="nexora-form-card-title">Konfigurasi Peran & Modul Izin</h4>
                            <p class="nexora-form-card-subtitle">Peran target terdaftar dan kumpulan izin operasional yang aktif saat ini.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            {{-- Role (Select2 / Read-only display) --}}
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Peran / Role Target <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-user-shield input-icon"></i>
                                    <select name="role_id" id="role_id" class="form-control nexora-input select2" required>
                                        <option value="{{ $role->id }}" selected>{{ $role->role }} (Aktif)</option>
                                        @foreach ($roles as $r)
                                            @if($r->id != $role->id)
                                                <option value="{{ $r->id }}">{{ $r->role }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Permission Selector --}}
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Tambah Modul Permission</label>
                                <div class="d-flex gap-2">
                                    <div class="nexora-input-wrap flex-grow-1">
                                        <i class="fas fa-key input-icon"></i>
                                        <select id="permissionSelect" class="form-control nexora-input select2">
                                            <option value="" disabled selected>-- Pilih Modul Hak Akses --</option>
                                            @foreach ($permissions as $permission)
                                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary ml-2 px-3" id="addPermission" style="border-radius: 12px; font-weight: 700; height: 46px; white-space: nowrap;">
                                        <i class="fas fa-plus mr-1"></i> Tambah
                                    </button>
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-info-circle mr-1"></i>Pilih izin baru dari dropdown lalu klik Tambah.</small>
                            </div>

                            {{-- Selected Permissions Box --}}
                            <div class="col-md-12 mb-3 mt-2">
                                <label class="nexora-input-label">Daftar Hak Akses yang Dimiliki Role Ini:</label>
                                <div id="selectedPermissions" class="p-3 rounded-lg" style="min-height: 80px; background: #F8FAFC; border: 1.5px dashed #CBD5E1; border-radius: 14px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
                                    @forelse ($role->permissions as $permission)
                                        <span class="badge badge-light px-3 py-2 d-inline-flex align-items-center selected-permission" data-id="{{ $permission->id }}" style="font-size: 12.5px; font-weight: 600; border-radius: 8px; background: #EBF4FC; color: #0058A6; border: 1px solid #BFDBFE;">
                                            <i class="fa fa-shield-alt mr-1"></i> {{ $permission->name }}
                                            <span style="cursor:pointer; font-size: 14px; font-weight: 800;" class="ml-2 text-danger remove-permission">&times;</span>
                                            <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                                        </span>
                                    @empty
                                        <p class="text-muted m-0" id="noPermissionText" style="font-size: 13px;"><i class="fa fa-info-circle mr-1"></i>Belum ada permission yang terpasang pada role ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/role-permissions/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Perbarui Hak Akses Role
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const permissionSelect = document.getElementById('permissionSelect');
        const addPermissionBtn = document.getElementById('addPermission');
        const selectedPermissionsDiv = document.getElementById('selectedPermissions');
        const noPermissionText = document.getElementById('noPermissionText');

        // Add Permission
        addPermissionBtn.addEventListener('click', function () {
            const selectedValue = permissionSelect.value;
            const selectedText = permissionSelect.options[permissionSelect.selectedIndex]?.text;

            if (selectedValue && !document.querySelector(`input[name="permissions[]"][value="${selectedValue}"]`)) {
                if (noPermissionText) noPermissionText.style.display = 'none';

                const badge = document.createElement('span');
                badge.className = 'badge badge-light px-3 py-2 d-inline-flex align-items-center selected-permission';
                badge.setAttribute('data-id', selectedValue);
                badge.style.fontSize = '12.5px';
                badge.style.fontWeight = '600';
                badge.style.borderRadius = '8px';
                badge.style.background = '#EBF4FC';
                badge.style.color = '#0058A6';
                badge.style.border = '1px solid #BFDBFE';
                badge.innerHTML = `
                    <i class="fa fa-shield-alt mr-1"></i> ${selectedText}
                    <span style="cursor:pointer; font-size: 14px; font-weight: 800;" class="ml-2 text-danger remove-permission">&times;</span>
                    <input type="hidden" name="permissions[]" value="${selectedValue}">
                `;

                selectedPermissionsDiv.appendChild(badge);
            }
        });

        // Remove Permission (event delegation)
        selectedPermissionsDiv.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-permission')) {
                const badge = e.target.closest('.selected-permission');
                badge.remove();

                if (!selectedPermissionsDiv.querySelector('input[name="permissions[]"]')) {
                    if (noPermissionText) {
                        noPermissionText.style.display = 'block';
                    } else {
                        selectedPermissionsDiv.innerHTML = '<p class="text-muted m-0" id="noPermissionText" style="font-size: 13px;"><i class="fa fa-info-circle mr-1"></i>Belum ada permission yang terpasang pada role ini.</p>';
                    }
                }
            }
        });
    });
</script>
@endsection
