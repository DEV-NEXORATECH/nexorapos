@extends('admin.layout.app')

@section('title', 'Tambah Perusahaan Baru - SaaS Control Center')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Simple & Clean Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> SAAS CONTROL CENTER &bull; ONBOARDING TENANT</span>
                        <h3 class="nexora-page-header-title">Registrasi Tenant Baru</h3>
                        <p class="nexora-page-header-desc">Daftarkan perusahaan baru, konfigurasi subdomain otomatis, paket lisensi, dan admin utama.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/features') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Features
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ url('admin-panel/features/company') }}">
                @csrf
                
                <!-- CARD 1: Profil & Identitas Perusahaan -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-emerald">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">INFORMASI BISNIS</span>
                            <h4 class="nexora-form-card-title">Profil & Identitas Perusahaan</h4>
                            <p class="nexora-form-card-subtitle">Pengaturan identitas resmi perusahaan tenant dan alokasi subdomain toko otomatis.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Nama Perusahaan / Brand <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-store input-icon"></i>
                                    <input type="text" name="name" class="form-control nexora-input" value="{{ old('name') }}" required placeholder="Contoh: Bu Winny POS">
                                </div>
                                @error('name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Kode Perusahaan (Auto)</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-barcode input-icon"></i>
                                    <input type="text" class="form-control nexora-input is-readonly" value="Auto: NAMA-COMPANY-001" disabled readonly>
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-info-circle mr-1"></i>Dihasilkan otomatis saat tenant disimpan.</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Domain Toko (Subdomain Auto)</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-globe input-icon"></i>
                                    <input type="text" id="company-domain-preview" class="form-control nexora-input is-readonly" value="Auto: nama-company.{{ config('services.hostinger.domain') }}" disabled readonly>
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-network-wired mr-1"></i>Subdomain Cloudflare/DNS dibuat otomatis.</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Nama Legal / PT / CV</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-landmark input-icon"></i>
                                    <input type="text" name="legal_name" class="form-control nexora-input" value="{{ old('legal_name') }}" placeholder="PT / CV Nama Resmi">
                                </div>
                                @error('legal_name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Nomor Pokok Wajib Pajak (NPWP)</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-receipt input-icon"></i>
                                    <input type="text" name="npwp" class="form-control nexora-input" value="{{ old('npwp') }}" placeholder="00.000.000.0-000.000">
                                </div>
                                @error('npwp')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Email Perusahaan</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" class="form-control nexora-input" value="{{ old('email') }}" placeholder="office@company.com">
                                </div>
                                @error('email')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Telepon Kantor</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="text" name="phone" class="form-control nexora-input" value="{{ old('phone') }}" placeholder="021-xxxx / 08xx">
                                </div>
                                @error('phone')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="nexora-input-label">Alamat Kantor / Outlet Utama</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-location-dot input-icon"></i>
                                    <input type="text" name="address" class="form-control nexora-input" value="{{ old('address') }}" placeholder="Alamat lengkap lokasi operasional">
                                </div>
                                @error('address')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Paket Lisensi & Billing -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-amber">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">LISENSI & PENAGIHAN</span>
                            <h4 class="nexora-form-card-title">Paket Lisensi & Aturan Billing</h4>
                            <p class="nexora-form-card-subtitle">Tentukan masa aktif awal, kuota kasir/user, periode penagihan, serta tenggat pembayaran.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Status Lisensi <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-circle-check input-icon text-success"></i>
                                    <select name="subscription_status" class="form-control nexora-input select2" required>
                                        <option value="active" {{ old('subscription_status', 'active') === 'active' ? 'selected' : '' }}>Active (Aktif)</option>
                                        <option value="past_due" {{ old('subscription_status') === 'past_due' ? 'selected' : '' }}>Past Due (Jatuh Tempo)</option>
                                        <option value="inactive" {{ old('subscription_status') === 'inactive' ? 'selected' : '' }}>Inactive (Non-aktif)</option>
                                        <option value="blocked" {{ old('subscription_status') === 'blocked' ? 'selected' : '' }}>Blocked (Terkunci)</option>
                                    </select>
                                </div>
                                @error('subscription_status')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Periode Penagihan <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-calendar-alt input-icon"></i>
                                    <select name="billing_period" class="form-control nexora-input nexora-billing-period select2" required>
                                        <option value="monthly" {{ old('billing_period', 'monthly') === 'monthly' ? 'selected' : '' }}>Bulanan (1 Bulan)</option>
                                        <option value="yearly" {{ old('billing_period') === 'yearly' ? 'selected' : '' }}>Tahunan (12 Bulan)</option>
                                        <option value="custom" {{ old('billing_period') === 'custom' ? 'selected' : '' }}>Kustom (Tentukan Bulan)</option>
                                    </select>
                                </div>
                                @error('billing_period')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Durasi Bulan <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-clock input-icon"></i>
                                    <input type="number" name="billing_interval_months" class="form-control nexora-input nexora-billing-months" value="{{ old('billing_interval_months', 1) }}" min="1" max="36" required>
                                </div>
                                @error('billing_interval_months')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Maksimal User / Staf <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-users input-icon"></i>
                                    <input type="number" name="max_users" class="form-control nexora-input" value="{{ old('max_users', 10) }}" min="1" max="100000" required>
                                </div>
                                @error('max_users')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Nominal Pembayaran (Rp)</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-money-bill-wave input-icon"></i>
                                    <input type="number" name="payment_amount" class="form-control nexora-input" value="{{ old('payment_amount') }}" min="0" step="1000" placeholder="Contoh: 150000">
                                </div>
                                @error('payment_amount')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Tanggal Mulai Lisensi</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-calendar-check input-icon"></i>
                                    <input type="date" name="license_started_at" class="form-control nexora-input" value="{{ old('license_started_at', now()->toDateString()) }}">
                                </div>
                                @error('license_started_at')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Jatuh Tempo (Due Date)</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-calendar-xmark input-icon"></i>
                                    <input type="date" name="payment_due_at" class="form-control nexora-input" value="{{ old('payment_due_at', now()->addMonth()->toDateString()) }}">
                                </div>
                                @error('payment_due_at')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Tenggat Toleransi (Hari) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-hourglass-half input-icon"></i>
                                    <input type="number" name="grace_period_days" class="form-control nexora-input" value="{{ old('grace_period_days', 7) }}" min="0" max="90" required>
                                </div>
                                @error('grace_period_days')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Administrator Utama (Initial Admin) -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">AKUN PENGELOLA</span>
                            <h4 class="nexora-form-card-title">Akun Administrator Utama Tenant (Opsional)</h4>
                            <p class="nexora-form-card-subtitle">Buat akun admin pertama agar pemilik perusahaan dapat langsung login dan mengonfigurasi tokonya.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Nama Admin</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" name="admin_name" class="form-control nexora-input" value="{{ old('admin_name') }}" placeholder="Nama PIC / Owner">
                                </div>
                                @error('admin_name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Email Admin (Login)</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-at input-icon"></i>
                                    <input type="email" name="admin_email" class="form-control nexora-input" value="{{ old('admin_email') }}" placeholder="admin@store.com">
                                </div>
                                @error('admin_email')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Nomor WhatsApp / HP</label>
                                <div class="nexora-input-wrap">
                                    <i class="fab fa-whatsapp input-icon"></i>
                                    <input type="text" name="admin_phone" class="form-control nexora-input" value="{{ old('admin_phone') }}" placeholder="08xxxxxxxxxx">
                                </div>
                                @error('admin_phone')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="nexora-input-label">Password Awal</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="admin_password" class="form-control nexora-input" placeholder="Minimal 8 karakter">
                                </div>
                                @error('admin_password')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/features') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-plus-circle mr-1"></i> Simpan & Daftarkan Perusahaan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    const companyNameInput = document.querySelector('input[name="name"]');
    const companyDomainPreview = document.querySelector('#company-domain-preview');
    const hostingerDomain = @json(config('services.hostinger.domain'));

    companyNameInput?.addEventListener('input', function () {
        const slug = this.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '') || 'nama-company';

        if (companyDomainPreview) {
            companyDomainPreview.value = 'Auto: ' + slug + '.' + hostingerDomain;
        }
    });

    $(document).on('change', '.nexora-billing-period', function() {
        const months = document.querySelector('.nexora-billing-months');
        if (!months) return;
        const val = $(this).val();
        if (val === 'monthly') {
            months.value = 1;
            months.readOnly = true;
        } else if (val === 'yearly') {
            months.value = 12;
            months.readOnly = true;
        } else {
            months.readOnly = false;
        }
    });
</script>
@endpush
