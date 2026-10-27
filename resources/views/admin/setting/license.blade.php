@extends('admin.layout.app')

@section('title', 'Status Lisensi & Paket SaaS - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Simple & Clean Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> PENGATURAN SISTEM &bull; LISENSI SAAS</span>
                        <h3 class="nexora-page-header-title">{{ auth()->user()?->isSuperAdmin() ? 'Kelola Lisensi & Tagihan Tenant' : 'Status Lisensi & Layanan Toko' }}</h3>
                        <p class="nexora-page-header-desc">
                            {{ auth()->user()?->isSuperAdmin()
                                ? 'Pantau status aktif tenant, jatuh tempo, kuota user, dan pembayaran.'
                                : 'Paket langganan aktif, masa berlaku aplikasi, kuota kasir, dan perpanjangan QRIS.' }}
                        </p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    @if(auth()->user()?->isSuperAdmin())
                        <a href="{{ url('admin-panel/features') }}" class="nexora-btn-back">
                            <i class="fas fa-toggle-on"></i> Hub Fitur Tenant
                        </a>
                    @else
                        <a href="{{ url('admin-panel/dashboard') }}" class="nexora-btn-back">
                            <i class="fa fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    @endif
                </div>
            </div>

            @foreach($companies as $company)
                @php
                    $license = $company->licenseSnapshot();
                    $enabledCount = 0;
                    $totalFeatures = $features->count();
                    $blocked = $license['is_locked'];
                    $licenseInactive = $license['is_locked'] || $company->isLicenseInactive();
                    $statusColor = match ($license['status']) {
                        'ACTIVE' => '#10B981',
                        'EXPIRING_SOON', 'GRACE_PERIOD' => '#F59E0B',
                        'OVERDUE', 'BLOCKED' => '#EF4444',
                        default => '#64748B',
                    };
                @endphp

                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding-bottom: 18px;">
                        <div class="d-flex align-items-center" style="gap: 14px;">
                            <div class="nexora-form-card-icon" style="background: rgba(0, 88, 166, 0.1); color: #0058A6;">
                                <i class="fas fa-store"></i>
                            </div>
                            <div>
                                <span class="nexora-form-card-eyebrow">TENANT CODE: {{ $company->company_code }}</span>
                                <h4 class="nexora-form-card-title mb-0">{{ $company->name }}</h4>
                                @if($company->payment_due_at)
                                    <small class="text-muted"><i class="fa fa-calendar mr-1"></i>Jatuh tempo: <strong>{{ $company->payment_due_at->format('d M Y') }}</strong></small>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="badge px-3 py-2" style="background: {{ $statusColor }}; color: #FFFFFF; font-size: 13px; font-weight: 700; border-radius: 20px;">
                                <i class="fa fa-circle mr-1" style="font-size: 8px;"></i> {{ $license['label'] }}
                            </span>
                        </div>
                    </div>

                    <div class="nexora-form-card-body pt-3">
                        <!-- METRIC HIGHLIGHTS -->
                        <div class="row mb-4">
                            <div class="col-6 col-md-3 col-lg mb-3">
                                <div class="p-3 rounded text-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <div class="text-muted mb-1" style="font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Paket Aktif</div>
                                    <div style="font-size: 15px; font-weight: 700; color: #0F1F3D;">{{ $license['package'] }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-lg mb-3">
                                <div class="p-3 rounded text-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <div class="text-muted mb-1" style="font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Mulai Aktif</div>
                                    <div style="font-size: 14.5px; font-weight: 700; color: #0F1F3D;">{{ optional($license['started_at'])->format('d M Y') ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-lg mb-3">
                                <div class="p-3 rounded text-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <div class="text-muted mb-1" style="font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Jatuh Tempo</div>
                                    <div style="font-size: 14.5px; font-weight: 700; color: #0F1F3D;">{{ optional($license['expiry_date'])->format('d M Y') ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-lg mb-3">
                                <div class="p-3 rounded text-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <div class="text-muted mb-1" style="font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Masa Tenggang</div>
                                    <div style="font-size: 15px; font-weight: 700; color: #0F1F3D;">{{ $license['grace_period_days'] }} Hari</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-lg mb-3">
                                <div class="p-3 rounded text-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <div class="text-muted mb-1" style="font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Tagihan</div>
                                    <div style="font-size: 15px; font-weight: 700; color: #0058A6;">{{ $license['payment_amount'] ? 'Rp '.number_format($license['payment_amount'], 0, ',', '.') : 'Gratis / Pro' }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-lg mb-3">
                                <div class="p-3 rounded text-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <div class="text-muted mb-1" style="font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Slot Pengguna</div>
                                    <div style="font-size: 15px; font-weight: 700; color: #0F1F3D;">{{ $company->activeUsersCount() }} / {{ $company->max_users ?: '&infin;' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- ALERTS -->
                        @if($license['is_locked'])
                            <div class="p-3 rounded mb-4 d-flex align-items-center" style="background: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; gap: 12px;">
                                <i class="fas fa-lock fa-2x"></i>
                                <div>
                                    <strong>Lisensi Terkunci ({{ $license['label'] }}).</strong>
                                    <div>{{ $company->block_reason ?: 'Masa aktif toko telah habis. Hubungi Super Admin atau perbarui pembayaran untuk membuka kembali akses kasir dan operasional.' }}</div>
                                </div>
                            </div>
                        @elseif($license['is_warning'])
                            <div class="p-3 rounded mb-4 d-flex align-items-center" style="background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; gap: 12px;">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                                <div>
                                    <strong>{{ $license['status'] === 'GRACE_PERIOD' ? 'Masa Tenggang (Grace Period) Sedang Berjalan.' : 'Pengingat Jatuh Tempo Pembayaran.' }}</strong>
                                    <div>
                                        @if($license['status'] === 'GRACE_PERIOD')
                                            Tersisa {{ max($license['remaining_grace_days'], 0) }} hari lagi sebelum akses fitur otomatis diblokir sistem.
                                        @else
                                            Tersisa {{ max($license['remaining_days'], 0) }} hari lagi sebelum tanggal jatuh tempo.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- FEATURES GRID -->
                        <div class="mb-3">
                            <span class="nexora-form-card-eyebrow mb-2 d-block">MODUL OPERASIONAL AKTIF</span>
                            <div class="row">
                                @foreach($features as $feature)
                                    @php
                                        $toggle = $companyFeatures[$company->id][$feature->key] ?? null;
                                        $enabled = $toggle ? $toggle->is_enabled : true;
                                        if ($enabled && ! $blocked) {
                                            $enabledCount++;
                                        }
                                    @endphp
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background: {{ $enabled && !$blocked ? '#F0FDF4' : '#F8FAFC' }}; border: 1px solid {{ $enabled && !$blocked ? '#BBF7D0' : '#E2E8F0' }};">
                                            <div>
                                                <strong style="color: {{ $enabled && !$blocked ? '#166534' : '#64748B' }}; font-size: 13.5px;">{{ $feature->name }}</strong>
                                                <small class="d-block text-muted" style="font-size: 11px;">{{ $feature->description ?: 'Akses modul toko' }}</small>
                                            </div>
                                            <span class="badge {{ $enabled && !$blocked ? 'badge-success' : 'badge-light' }}" style="font-size: 11px;">
                                                {{ $enabled && !$blocked ? 'Aktif' : 'Non-aktif' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- BOTTOM SUMMARY & PAYMENT -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap pt-3" style="border-top: 1px solid #E6EBF3; gap: 12px;">
                            <div class="text-muted" style="font-size: 12.5px;">
                                Total Fitur Aktif: <strong>{{ $enabledCount }} / {{ $totalFeatures }}</strong>
                                &bull; Sisa Kuota User: <strong>{{ is_null($company->remainingUserSlots()) ? 'Unlimited' : $company->remainingUserSlots() }}</strong>
                                @if($company->block_reason)
                                    &bull; Catatan: <em>{{ $company->block_reason }}</em>
                                @endif
                            </div>

                            @if(!auth()->user()?->isSuperAdmin() && (float) $company->payment_amount > 0)
                                <div>
                                    <button type="button" class="btn btn-success license-qris-btn" data-company-id="{{ $company->id }}" style="border-radius: 10px; font-weight: 700; padding: 10px 24px; background: #10B981; border: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                                        <i class="fas fa-qrcode mr-1"></i> Bayar Perpanjangan via QRIS (Rp {{ number_format($company->payment_amount, 0, ',', '.') }})
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
@endsection

@push('script')
    @if(!auth()->user()?->isSuperAdmin() && $companies->contains(fn ($company) => (float) $company->payment_amount > 0))
        <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
        <script>
            document.querySelectorAll('.license-qris-btn').forEach((button) => {
                button.addEventListener('click', async () => {
                    button.disabled = true;
                    try {
                        const response = await fetch('{{ url('admin-panel/settings/license/payment/qris') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({}),
                        });
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Pembayaran gagal dibuat.');
                        window.snap.pay(data.snap_token, {
                            onSuccess: () => Swal.fire('Berhasil', 'Pembayaran diterima. License akan diperpanjang otomatis.', 'success').then(() => window.location.reload()),
                            onPending: () => Swal.fire('Menunggu pembayaran', 'Selesaikan pembayaran QRIS di Midtrans.', 'info'),
                            onError: () => Swal.fire('Gagal', 'Pembayaran QRIS gagal.', 'error'),
                            onClose: () => { button.disabled = false; },
                        });
                    } catch (error) {
                        button.disabled = false;
                        Swal.fire('Gagal', error.message, 'error');
                    }
                });
            });
        </script>
    @endif
    @unless(auth()->user()?->isSuperAdmin())
        @foreach($companies as $company)
            @php
                $license = $company->licenseSnapshot();
            @endphp
            @if($license['is_locked'])
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'License {{ $license['label'] }}',
                        text: @json(
                            'Company '.$company->name.' sedang tidak aktif. '.
                            'Paket: '.$license['package'].'. '.
                            'Jatuh tempo: '.(optional($license['expiry_date'])->format('d M Y') ?? '-').'. '.
                            'Fitur operasional diblok sampai pembayaran dikonfirmasi Super Admin.'
                        ),
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#0058A6',
                        backdrop: true,
                    });
                </script>
            @elseif($license['is_warning'])
                @php
                    $licenseWarningMessage = 'Paket '.$license['package'].' jatuh tempo pada '.
                        optional($license['expiry_date'])->format('d M Y').'. ';
                    $licenseWarningMessage .= $license['status'] === 'GRACE_PERIOD'
                        ? 'Sisa grace '.max($license['remaining_grace_days'], 0).' hari. '
                        : 'Sisa '.max($license['remaining_days'], 0).' hari. ';
                    $licenseWarningMessage .= 'Jika lewat masa aktif/grace, fitur akan otomatis diblok.';
                @endphp
                <script>
                    Swal.fire({
                        icon: 'warning',
                        title: @json($license['status'] === 'GRACE_PERIOD' ? 'Grace Period License' : 'License Hampir Jatuh Tempo'),
                        text: @json($licenseWarningMessage),
                        confirmButtonText: 'Saya Mengerti',
                        confirmButtonColor: '#0058A6',
                    });
                </script>
            @endif
        @endforeach
    @endunless
@endpush
