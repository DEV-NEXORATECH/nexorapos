@extends('admin.layout.app')

@section('title', 'Perbarui Pengguna - ' . $user->name)

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Simple & Clean Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> USER & AKSES &bull; EDIT USER</span>
                        <h3 class="nexora-page-header-title">Edit Pengguna: {{ $user->name }}</h3>
                        <p class="nexora-page-header-desc">Perbarui identitas profil, penugasan peranan role, lokasi outlet cabang, dan kredensial staf.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/users/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/users/update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- CARD 1: Informasi Pengguna & Peranan -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-emerald">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">IDENTITAS STAF</span>
                            <h4 class="nexora-form-card-title">Informasi Pengguna & Hak Akses</h4>
                            <p class="nexora-form-card-subtitle">Nama lengkap, email login, telepon, dan pemilihan peranan operasional.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" name="name" id="name" class="form-control nexora-input" value="{{ old('name', $user->name) }}" required>
                                </div>
                                @error('name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Alamat Email (Login) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" id="email" class="form-control nexora-input" value="{{ old('email', $user->email) }}" required>
                                </div>
                                @error('email')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="text" name="phone" id="phone" class="form-control nexora-input" value="{{ old('phone', $user->phone) }}" required>
                                </div>
                                @error('phone')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Peran / Role Otoritas <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-shield-halved input-icon"></i>
                                    <select name="role_id" id="role" class="form-control nexora-input" required>
                                        <option value="">-- Pilih Role Peran --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
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

                            @include('admin.partials.company-selector', ['selectedCompanyId' => $user->company_id])
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
                                    <select name="store_id" id="store_id" class="form-control nexora-input">
                                        <option value="">Akses Seluruh Toko (Owner / General Manager)</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" data-company="{{ $store->company_id }}" @selected(old('store_id', $user->store_id) == $store->id)>
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
                                    <input type="text" name="shift_name" id="shift_name" class="form-control nexora-input" value="{{ old('shift_name', $user->shift_name) }}" placeholder="Contoh: Shift Pagi / Kasir Utama">
                                </div>
                                @error('shift_name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Jam Mulai Shift</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-clock input-icon"></i>
                                    <input type="time" name="shift_start" id="shift_start" class="form-control nexora-input" value="{{ old('shift_start', $user->shift_start) }}">
                                </div>
                                @error('shift_start')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Jam Selesai Shift</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-stopwatch input-icon"></i>
                                    <input type="time" name="shift_end" id="shift_end" class="form-control nexora-input" value="{{ old('shift_end', $user->shift_end) }}">
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
                            <span class="nexora-form-card-eyebrow">KEAMANAN & FOTO</span>
                            <h4 class="nexora-form-card-title">Foto Profil & Pembaruan Sandi</h4>
                            <p class="nexora-form-card-subtitle">Ubah foto identitas staf atau setel kata sandi baru (kosongkan jika tidak ingin mengganti kata sandi).</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row align-items-center mb-4">
                            <div class="col-auto">
                                <div class="nexora-avatar-preview" style="width: 72px; height: 72px; border-radius: 50%; overflow: hidden; background: #0058A6; color: #FFF; display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 700; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                                    @if($user->profile_image)
                                        <img src="{{ asset('admin/profile_images/' . $user->profile_image) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <label class="nexora-input-label mb-1">Ganti Foto Profil</label>
                                <input type="file" name="image" id="profile_image" class="form-control-file" accept="image/png,image/jpeg,image/jpg,image/webp">
                                <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Format didukung: PNG, JPG, JPEG, WEBP. Maksimal 2MB.</small>
                                @error('image')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Kata Sandi Baru</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-key input-icon"></i>
                                    <input type="password" name="password" id="password" class="form-control nexora-input" placeholder="Biarkan kosong jika tetap menggunakan sandi lama">
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-info-circle mr-1"></i>Hanya diisi jika ingin mereset password staf ini.</small>
                                @error('password')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Konfirmasi Kata Sandi Baru</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-check-double input-icon"></i>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control nexora-input" placeholder="Ketik ulang kata sandi baru">
                                </div>
                                @error('password_confirmation')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
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
                        <i class="fas fa-save mr-1"></i> Perbarui Data Pengguna
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
