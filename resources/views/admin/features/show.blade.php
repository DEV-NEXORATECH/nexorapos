@extends('admin.layout.app')

@section('title', 'Kelola Fitur & Billing: ' . $company->name)

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            @php
                $license = $company->licenseSnapshot();
                $statusColor = match ($license['status']) {
                    'ACTIVE' => '#10B981',
                    'EXPIRING_SOON', 'GRACE_PERIOD' => '#F59E0B',
                    'OVERDUE', 'BLOCKED' => '#EF4444',
                    default => '#64748B',
                };
            @endphp

            <!-- Simple & Clean Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> SAAS CONTROL CENTER &bull; TENANT MANAGEMENT</span>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <h3 class="nexora-page-header-title mb-0">{{ $company->name }}</h3>
                            <span class="badge badge-light px-2 py-1" style="font-weight: 700; color: #0058A6; font-size: 11.5px;">{{ $company->company_code }}</span>
                            <span class="badge px-2 py-1" style="background: {{ $statusColor }}; color: #FFFFFF; font-weight: 700; font-size: 11px; border-radius: 6px;">{{ $license['label'] }}</span>
                        </div>
                        <p class="nexora-page-header-desc">Atur paket lisensi, masa tenggang, dan izin fitur aktif untuk tenant ini.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/features') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Features
                    </a>
                </div>
            </div>

            <!-- CARD 1: Konfigurasi Billing & Pembayaran -->
            <form method="POST" action="{{ url('admin-panel/features/billing') }}" class="nexora-billing-form nexora-form-card mb-4">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">

                <div class="nexora-form-card-head">
                    <div class="nexora-form-card-icon is-amber">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <span class="nexora-form-card-eyebrow">BILLING & ACCESS CONTROL</span>
                        <h4 class="nexora-form-card-title">Pengaturan Langganan & Masa Aktif</h4>
                        <p class="nexora-form-card-subtitle">Perbarui status langganan, masa berlaku, interval pembayaran, dan limitasi kuota pengguna.</p>
                    </div>
                </div>

                <div class="nexora-form-card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="nexora-input-label">Status Langganan</label>
                            <div class="nexora-input-wrap">
                                <i class="fas fa-toggle-on input-icon"></i>
                                <select name="subscription_status" class="form-control nexora-input">
                                    <option value="active" {{ ($company->subscription_status ?? 'active') === 'active' ? 'selected' : '' }}>Active (Aktif Normal)</option>
                                    <option value="past_due" {{ $company->subscription_status === 'past_due' ? 'selected' : '' }}>Past Due (Jatuh Tempo)</option>
                                    <option value="inactive" {{ $company->subscription_status === 'inactive' ? 'selected' : '' }}>Inactive (Non-aktif)</option>
                                    <option value="blocked" {{ $company->subscription_status === 'blocked' ? 'selected' : '' }}>Blocked (Terkunci)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="nexora-input-label">Periode Pembayaran</label>
                            <div class="nexora-input-wrap">
                                <i class="fas fa-calendar-alt input-icon"></i>
                                <select name="billing_period" class="form-control nexora-input nexora-billing-period">
                                    <option value="monthly" {{ ($company->billing_period ?? 'monthly') === 'monthly' ? 'selected' : '' }}>Bulanan (1 Bulan)</option>
                                    <option value="yearly" {{ $company->billing_period === 'yearly' ? 'selected' : '' }}>Tahunan (12 Bulan)</option>
                                    <option value="custom" {{ $company->billing_period === 'custom' ? 'selected' : '' }}>Kustom</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="nexora-input-label">Durasi Bulan</label>
                            <div class="nexora-input-wrap">
                                <i class="fas fa-clock input-icon"></i>
                                <input type="number" name="billing_interval_months" class="form-control nexora-input nexora-billing-months" value="{{ $company->billing_interval_months ?: 1 }}" min="1" max="36">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="nexora-input-label">Nominal Tagihan (Rp)</label>
                            <div class="nexora-input-wrap">
                                <i class="fas fa-money-bill-wave input-icon"></i>
                                <input type="number" name="payment_amount" class="form-control nexora-input" value="{{ $company->payment_amount }}" min="0" step="1000" placeholder="Optional">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="nexora-input-label">Tanggal Mulai Lisensi</label>
                            <div class="nexora-input-wrap">
                                <i class="fas fa-calendar-check input-icon"></i>
                                <input type="date" name="license_started_at" class="form-control nexora-input" value="{{ optional($company->license_started_at)->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="nexora-input-label">Maksimal User / Staf</label>
                            <div class="nexora-input-wrap">
                                <i class="fas fa-users input-icon"></i>
                                <input type="number" name="max_users" class="form-control nexora-input" value="{{ $company->max_users ?: 10 }}" min="1" max="100000">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="nexora-input-label">Jatuh Tempo Pembayaran</label>
                            <div class="nexora-input-wrap">
                                <i class="fas fa-calendar-xmark input-icon"></i>
                                <input type="date" name="payment_due_at" class="form-control nexora-input" value="{{ optional($company->payment_due_at)->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="nexora-input-label">Tenggat Toleransi (Hari)</label>
                            <div class="nexora-input-wrap">
                                <i class="fas fa-hourglass-half input-icon"></i>
                                <input type="number" name="grace_period_days" class="form-control nexora-input" value="{{ $company->grace_period_days ?? 7 }}" min="0" max="90">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="nexora-input-label">Catatan Billing / Keterangan</label>
                            <div class="nexora-input-wrap">
                                <i class="fas fa-sticky-note input-icon"></i>
                                <input type="text" name="block_reason" class="form-control nexora-input" value="{{ $company->block_reason }}" placeholder="Contoh: Menunggu transfer bank">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nexora-form-action-bar" style="margin-top: 10px; border-top: 1px solid #E6EBF3; padding-top: 18px;">
                    <button type="submit" name="billing_action" value="save" class="btn btn-secondary" style="border-radius: 10px; font-weight: 600; padding: 10px 22px;">
                        <i class="fas fa-save mr-1"></i> Simpan Billing
                    </button>
                    <button type="submit" name="billing_action" value="mark_paid" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; padding: 10px 24px; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-check-circle mr-1"></i> Tandai Lunas & Perpanjang
                    </button>
                    @if((float) $company->payment_amount > 0)
                        <button type="button" class="btn btn-success" id="payCompanyQrisBtn" style="border-radius: 10px; font-weight: 700; padding: 10px 22px; background: #10B981; border: none;">
                            <i class="fas fa-qrcode mr-1"></i> Bayar via QRIS Midtrans
                        </button>
                    @endif
                </div>
            </form>

            <!-- CARD 2: Kelola Fitur Modul (Feature Matrix) -->
            <form method="POST" action="{{ url('admin-panel/features/update') }}" class="nexora-form-card mb-5">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">

                <div class="nexora-form-card-head d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                    <div class="d-flex align-items-center" style="gap: 14px;">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">MODUL OPERASIONAL</span>
                            <h4 class="nexora-form-card-title">Hak Akses Modul & Fitur</h4>
                            <p class="nexora-form-card-subtitle">Aktifkan atau nonaktifkan fitur spesifik untuk disesuaikan dengan paket berlangganan toko ini.</p>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; padding: 10px 24px;">
                        <i class="fas fa-shield mr-1"></i> Simpan Hak Akses Fitur
                    </button>
                </div>

                <div class="nexora-form-card-body">
                    <div class="nexora-feature-toggle-grid">
                        @foreach ($features as $feature)
                            @php
                                $toggle = $companyFeatures[$feature->key] ?? null;
                                $enabled = $toggle ? $toggle->is_enabled : true;
                            @endphp
                            <label class="nexora-feature-toggle {{ $enabled ? 'is-enabled' : 'is-disabled' }}">
                                <input type="checkbox" name="features[]" value="{{ $feature->key }}" {{ $enabled ? 'checked' : '' }}>
                                <span class="nexora-toggle-visual"></span>
                                <span class="nexora-feature-toggle-copy">
                                    <strong>{{ $feature->name }}</strong>
                                    <small>{{ $feature->description ?: 'Modul operasional sistem Nexora' }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('script')
    @if((float) $company->payment_amount > 0)
        <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif
    <script>
        document.getElementById('payCompanyQrisBtn')?.addEventListener('click', async function () {
            const button = this;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan QRIS...';

            try {
                const response = await fetch('{{ url('admin-panel/features/payment/qris') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({company_id: {{ $company->id }}}),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Pembayaran gagal dibuat.');

                window.snap.pay(data.snap_token, {
                    onSuccess: () => Swal.fire('Berhasil', 'Pembayaran diterima. Status license akan diperbarui otomatis.', 'success').then(() => window.location.reload()),
                    onPending: () => Swal.fire('Menunggu pembayaran', 'Selesaikan pembayaran QRIS pada jendela Midtrans.', 'info'),
                    onError: () => Swal.fire('Gagal', 'Pembayaran QRIS gagal.', 'error'),
                    onClose: () => { button.disabled = false; button.innerHTML = '<i class="fas fa-qrcode"></i> Bayar via QRIS Midtrans'; },
                });
            } catch (error) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-qrcode"></i> Bayar via QRIS Midtrans';
                Swal.fire('Gagal', error.message, 'error');
            }
        });

        document.querySelectorAll('.nexora-billing-form').forEach((form) => {
            const period = form.querySelector('.nexora-billing-period');
            const months = form.querySelector('.nexora-billing-months');

            if (!period || !months) {
                return;
            }

            const syncMonths = () => {
                if (period.value === 'monthly') {
                    months.value = 1;
                    months.readOnly = true;
                } else if (period.value === 'yearly') {
                    months.value = 12;
                    months.readOnly = true;
                } else {
                    months.readOnly = false;
                }
            };

            period.addEventListener('change', syncMonths);
            syncMonths();
        });

        document.querySelectorAll('.nexora-feature-toggle input[type="checkbox"]').forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const card = checkbox.closest('.nexora-feature-toggle');
                if (!card) {
                    return;
                }

                card.classList.toggle('is-enabled', checkbox.checked);
                card.classList.toggle('is-disabled', !checkbox.checked);
            });
        });
    </script>
@endpush
