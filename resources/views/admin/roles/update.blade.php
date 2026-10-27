@extends('admin.layout.app')

@section('title', 'Perbarui Peran - ' . $roles->role)

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Simple & Clean Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> USER & AKSES &bull; EDIT ROLE</span>
                        <h3 class="nexora-page-header-title">Edit Peran: {{ $roles->role }}</h3>
                        <p class="nexora-page-header-desc">Perbarui hak akses dan izin operasi untuk peran ini di seluruh modul sistem POS.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/roles/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/roles/update', $roles->id) }}" method="POST" id="categoryForm">
                @csrf
                
                <!-- CARD 1: Nama Role -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">IDENTITAS PERAN</span>
                            <h4 class="nexora-form-card-title">Nama Peran / Jabatan</h4>
                            <p class="nexora-form-card-subtitle">Nama role yang mewakili sekelompok staf operasional.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="nexora-input-label">Nama Role <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-shield-alt input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="role" id="role" value="{{ old('role', $roles->role) }}" required>
                                </div>
                                @error('role')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Matriks Hak Akses Modul (Permissions) -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                        <div class="d-flex align-items-center" style="gap: 14px;">
                            <div class="nexora-form-card-icon is-amber">
                                <i class="fas fa-key"></i>
                            </div>
                            <div>
                                <span class="nexora-form-card-eyebrow">MATRIKS OTORISASI</span>
                                <h4 class="nexora-form-card-title">Hak Akses Modul & Tindakan</h4>
                                <p class="nexora-form-card-subtitle">Centang atau hilangkan centang hak akses yang diizinkan untuk peran ini.</p>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllPermissions" style="border-radius: 20px; font-weight: 600; padding: 6px 16px;">
                                <i class="fas fa-check-double mr-1"></i> Pilih Semua Hak Akses
                            </button>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        @foreach($permissionCategories as $category)
                            <div class="nexora-permission-group mb-4 p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1px solid #E2E8F0;">
                                    <div class="d-flex align-items-center" style="gap: 10px;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(0, 88, 166, 0.1); color: #0058A6; font-size: 14px;">
                                            <i class="{{ $category->icons ?? $category->icon ?? 'fas fa-folder-open' }}"></i>
                                        </div>
                                        <strong style="color: #0F1F3D; font-size: 14.5px;">{{ $category->name }}</strong>
                                        <span class="badge badge-light" style="font-size: 11px; color: #64748B;">{{ count($category->permissions) }} aksi</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary js-select-row" style="border-radius: 14px; font-size: 11px; padding: 3px 12px; font-weight: 600;">
                                        Centang Modul Ini
                                    </button>
                                </div>
                                <div class="row">
                                    @foreach($category->permissions as $permission)
                                        <div class="col-md-3 col-sm-6 mb-2">
                                            <label class="d-flex align-items-center p-2 rounded mb-0" style="background: #FFFFFF; border: 1px solid #E2E8F0; cursor: pointer; transition: all 0.2s ease;">
                                                <input type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}
                                                    style="width: 16px; height: 16px; accent-color: #0058A6; margin-right: 8px;">
                                                <span style="font-size: 12.5px; font-weight: 500; color: #334155;">{{ $permission->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/roles/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Perbarui Data Peran
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let allChecked = false;
    document.getElementById('selectAllPermissions')?.addEventListener('click', function () {
        allChecked = !allChecked;
        document.querySelectorAll('input[name="permissions[]"]').forEach((input) => input.checked = allChecked);
        this.innerHTML = allChecked ? '<i class="fas fa-times mr-1"></i> Batal Pilih Semua' : '<i class="fas fa-check-double mr-1"></i> Pilih Semua Hak Akses';
    });

    document.querySelectorAll('.js-select-row').forEach(function (button) {
        button.addEventListener('click', function () {
            const container = button.closest('.nexora-permission-group');
            if (!container) return;
            const checkboxes = container.querySelectorAll('input[name="permissions[]"]');
            const anyUnchecked = Array.from(checkboxes).some(cb => !cb.checked);
            checkboxes.forEach(cb => cb.checked = anyUnchecked);
            button.innerText = anyUnchecked ? 'Batal Centang Modul' : 'Centang Modul Ini';
        });
    });
});
</script>
@endpush
