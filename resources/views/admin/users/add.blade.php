@extends('admin.layout.app')

@section('title', 'Tambah Pengguna Baru - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Simple & Clean Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> USER & AKSES &bull; PENGGUNA BARU</span>
                        <h3 class="nexora-page-header-title">Tambah Pengguna Baru</h3>
                        <p class="nexora-page-header-desc">Daftarkan akun kasir, supervisor, atau staf toko dengan peranan dan hak akses yang aman.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/users/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/users/add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- CARD 1: Informasi Pribadi & Otoritas -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-emerald">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">IDENTITAS STAF</span>
                            <h4 class="nexora-form-card-title">Informasi Pengguna & Hak Akses</h4>
                            <p class="nexora-form-card-subtitle">Nama lengkap, kontak resmi, pemilihan role peranan, dan penugasan perusahaan.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" name="name" id="name" class="form-control nexora-input" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required>
                                </div>
                                @error('name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Alamat Email (Login) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" id="email" class="form-control nexora-input" value="{{ old('email') }}" placeholder="budi@company.com" required>
                                </div>
                                @error('email')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="text" name="phone" id="phone" class="form-control nexora-input" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                                </div>
                                @error('phone')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Peran / Role Otoritas <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-shield-halved input-icon"></i>
                                    <select name="role_id" id="role_id" class="form-control nexora-input select2" required>
                                        <option value="">-- Pilih Role Peran --</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                                {{ $role->role }}
                                                @if(auth()->user()?->isSuperAdmin() && $role->company)
                                                    - {{ $role->company->company_code }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('role_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            @include('admin.partials.company-selector')

                            @if(auth()->user()?->isSuperAdmin())
                                <div class="col-md-12 mt-2 mb-2">
                                    <div class="p-3 rounded" style="background: rgba(0, 88, 166, 0.05); border: 1px solid rgba(0, 88, 166, 0.15);">
                                        <label class="d-flex align-items-center mb-0" style="cursor: pointer;">
                                            <input type="checkbox" name="is_super_admin" value="1" @checked(old('is_super_admin')) style="width: 18px; height: 18px; accent-color: #0058A6; margin-right: 10px;">
                                            <div>
                                                <strong style="color: #0F1F3D; font-size: 13.5px;">Hak Akses Super Admin Platform</strong>
                                                <div class="text-muted" style="font-size: 12px;">Berikan tanda centang jika user ini berwenang mengelola seluruh tenant dan konfigurasi global sistem SaaS.</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Penugasan Toko / Cabang & Shift Kerja -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">PENEMPATAN KERJA</span>
                            <h4 class="nexora-form-card-title">Penugasan Outlet & Shift Kerja</h4>
                            <p class="nexora-form-card-subtitle">Tentukan lokasi toko tempat bertugas dan jadwal jam kerja operasional.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Toko / Cabang Bertugas</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-shop input-icon"></i>
                                    <select name="store_id" id="store_id" class="form-control nexora-input select2">
                                        <option value="">Akses Seluruh Toko (Owner / General Manager)</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" data-company="{{ $store->company_id }}" @selected(old('store_id') == $store->id)>
                                                {{ $store->name }}
                                                @if(auth()->user()?->isSuperAdmin() && $store->company)
                                                    - {{ $store->company->company_code }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-info-circle mr-1"></i>Kosongkan jika staf diizinkan mengakses transaksi di seluruh outlet.</small>
                                @error('store_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Shift Kerja</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-calendar-day input-icon"></i>
                                    <input type="text" name="shift_name" id="shift_name" class="form-control nexora-input" value="{{ old('shift_name') }}" placeholder="Contoh: Shift Pagi / Kasir Utama">
                                </div>
                                @error('shift_name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Jam Mulai Shift</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-clock input-icon"></i>
                                    <input type="time" name="shift_start" id="shift_start" class="form-control nexora-input" value="{{ old('shift_start') }}">
                                </div>
                                @error('shift_start')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Jam Selesai Shift</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-stopwatch input-icon"></i>
                                    <input type="time" name="shift_end" id="shift_end" class="form-control nexora-input" value="{{ old('shift_end') }}">
                                </div>
                                @error('shift_end')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Keamanan & Foto Profil -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-amber">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">KEAMANAN & KREDENSIAL</span>
                            <h4 class="nexora-form-card-title">Kredensial Password & Foto Profil</h4>
                            <p class="nexora-form-card-subtitle">Buat kata sandi login awal yang kuat dan lampirkan foto identitas staf.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Kata Sandi (Password) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-key input-icon"></i>
                                    <input type="password" name="password" id="password" class="form-control nexora-input" placeholder="Minimal 8 karakter" required>
                                </div>
                                @error('password')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-check-double input-icon"></i>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control nexora-input" placeholder="Ketik ulang kata sandi" required>
                                </div>
                                @error('password_confirmation')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="nexora-input-label">Foto Profil Staf (Opsional)</label>
                                <div class="nexora-file-dropzone p-3 rounded d-flex align-items-center" style="border: 2px dashed #CBD5E1; background: #F8FAFC; gap: 16px;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #E2E8F0; color: #64748B; font-size: 20px;">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="image" id="profile_image" class="form-control-file" accept="image/png,image/jpeg,image/jpg,image/webp">
                                        <div class="text-muted mt-1" style="font-size: 11.5px;">Format didukung: PNG, JPG, JPEG, WEBP. Maksimal 2MB.</div>
                                    </div>
                                </div>
                                @error('image')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/users/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-user-check mr-1"></i> Simpan Pengguna Baru
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
        const companySelect = document.getElementById('company_id');
        const storeSelect = document.getElementById('store_id');

        if (!companySelect || !storeSelect) {
            return;
        }

        const syncStores = () => {
            const companyId = companySelect.value;

            Array.from(storeSelect.options).forEach((option) => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                const isMatch = !companyId || option.dataset.company === companyId;
                option.hidden = !isMatch;

                if (!isMatch && option.selected) {
                    storeSelect.value = '';
                }
            });

            if (window.jQuery && window.jQuery.fn.select2) {
                window.jQuery(storeSelect).trigger('change.select2');
            }
        };

        companySelect.addEventListener('change', syncStores);
        syncStores();
    });
</script>
@endsection
