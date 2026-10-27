@extends('admin.layout.app')

@section('title', 'Hubungkan Hak Akses Peran - NexoraPOS')

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
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> USER & AKSES &bull; ROLE PERMISSIONS</span>
                        <h3 class="nexora-page-header-title">Hubungkan Hak Akses ke Peran</h3>
                        <p class="nexora-page-header-desc">Alokasikan modul dan batasan permission baru pada suatu role operasional sistem.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/role-permissions/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Matriks
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/role-permissions/add') }}" method="POST">
                @csrf
                
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">PEMETAAN IZIN</span>
                            <h4 class="nexora-form-card-title">Pilih Role & Modul Hak Akses</h4>
                            <p class="nexora-form-card-subtitle">Pilih peran target dan tambahkan modul izin yang diizinkan untuk diakses.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            {{-- Select Role --}}
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Pilih Peran / Role <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-user-shield input-icon"></i>
                                    <select name="role_id" id="role_id" class="form-control nexora-input select2" required>
                                        <option value="">-- Pilih Role Target --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Permission Selector --}}
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Pilih Modul Permission</label>
                                <div class="d-flex gap-2">
                                    <div class="nexora-input-wrap flex-grow-1">
                                        <i class="fas fa-key input-icon"></i>
                                        <select id="permissionSelect" class="form-control nexora-input select2">
                                            <option value="">-- Pilih Modul Hak Akses --</option>
                                            @foreach ($permissions as $permission)
                                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary ml-2 px-3" id="addPermission" style="border-radius: 12px; font-weight: 700; height: 46px; white-space: nowrap;">
                                        <i class="fas fa-plus mr-1"></i> Tambah
                                    </button>
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-info-circle mr-1"></i>Pilih izin satu per satu lalu tekan Tambah.</small>
                            </div>

                            {{-- Selected Permissions Box --}}
                            <div class="col-md-12 mb-3 mt-2">
                                <label class="nexora-input-label">Daftar Hak Akses yang Dipilih:</label>
                                <div id="selectedPermissions" class="p-3 rounded-lg" style="min-height: 80px; background: #F8FAFC; border: 1.5px dashed #CBD5E1; border-radius: 14px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
                                    <p class="text-muted m-0" id="noPermissionText" style="font-size: 13px;"><i class="fa fa-info-circle mr-1"></i>Belum ada permission yang ditambahkan ke peran ini.</p>
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
                        <i class="fas fa-save mr-1"></i> Simpan Pemetaan Permission
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const permissionSelect = document.getElementById('permissionSelect');
        const addPermissionBtn = document.getElementById('addPermission');
        const selectedPermissionsDiv = document.getElementById('selectedPermissions');
        const noPermissionText = document.getElementById('noPermissionText');

        addPermissionBtn.addEventListener('click', function() {
            const selectedValue = permissionSelect.value;
            const selectedText = permissionSelect.options[permissionSelect.selectedIndex]?.text;

            if (selectedValue && !document.querySelector(`input[name="permissions[]"][value="${selectedValue}"]`)) {
                if (noPermissionText) noPermissionText.style.display = 'none';

                // Create chip
                const chip = document.createElement('span');
                chip.className = 'badge badge-light px-3 py-2 d-inline-flex align-items-center';
                chip.style.fontSize = '12.5px';
                chip.style.fontWeight = '600';
                chip.style.borderRadius = '8px';
                chip.style.background = '#EBF4FC';
                chip.style.color = '#0058A6';
                chip.style.border = '1px solid #BFDBFE';
                chip.innerHTML = `<i class="fa fa-shield-alt mr-1"></i> ${selectedText} <span style="cursor:pointer; font-size: 14px; font-weight: 800;" class="ml-2 text-danger remove-btn">&times;</span>`;

                // Hidden input to send with form
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'permissions[]';
                hiddenInput.value = selectedValue;

                // Remove chip on click
                chip.querySelector('.remove-btn').addEventListener('click', function() {
                    chip.remove();
                    hiddenInput.remove();
                    if (!selectedPermissionsDiv.querySelector('input[name="permissions[]"]')) {
                        if (noPermissionText) noPermissionText.style.display = 'block';
                    }
                });

                selectedPermissionsDiv.appendChild(chip);
                selectedPermissionsDiv.appendChild(hiddenInput);
            }
        });
    });
</script>
@endsection
