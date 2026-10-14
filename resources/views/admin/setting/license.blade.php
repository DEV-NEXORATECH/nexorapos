@extends('admin.layout.app')

@section('title', 'License & Features')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 12px;">
                <div>
                    <h4 class="page-title mb-1">{{ auth()->user()?->isSuperAdmin() ? 'License & Features' : 'License Status' }}</h4>
                    <p class="text-muted mb-0">
                        {{ auth()->user()?->isSuperAdmin()
                            ? 'Status lisensi company, pembayaran, dan fitur yang sedang aktif.'
                            : 'Bukti status pembayaran, paket aktif, masa berlaku, dan fitur yang bisa dipakai company kamu.' }}
                    </p>
                </div>
                @if(auth()->user()?->isSuperAdmin())
                    <a href="{{ url('admin-panel/features') }}" class="btn btn-primary btn-sm">Manage Features</a>
                @endif
            </div>

            @foreach($companies as $company)
                @php
                    $license = $company->licenseSnapshot();
                    $enabledCount = 0;
                    $totalFeatures = $features->count();
                    $blocked = $license['is_locked'];
                    $licenseInactive = $license['is_locked'] || $company->isLicenseInactive();
                @endphp
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-start flex-wrap" style="gap: 12px;">
                        <div>
                            <h4 class="card-title mb-1">{{ $company->name }}</h4>
                            <p class="card-category mb-0">
                                Code: <strong>{{ $company->company_code }}</strong>
                                @if($company->payment_due_at)
                                    | Due: <strong>{{ $company->payment_due_at->format('d M Y') }}</strong>
                                @endif
                            </p>
                        </div>
                        <span class="nexora-status nexora-status-{{ $license['badge_class'] }}">
                            {{ $license['label'] }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-2 mb-3">
                                <div class="nexora-license-summary">
                                    <small>Status</small>
                                    <strong>{{ $license['label'] }}</strong>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="nexora-license-summary">
                                    <small>Paket Aktif</small>
                                    <strong>{{ $license['package'] }}</strong>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="nexora-license-summary">
                                    <small>Mulai</small>
                                    <strong>{{ optional($license['started_at'])->format('d M Y') ?? '-' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="nexora-license-summary">
                                    <small>Jatuh Tempo</small>
                                    <strong>{{ optional($license['expiry_date'])->format('d M Y') ?? '-' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="nexora-license-summary">
                                    <small>Grace</small>
                                    <strong>{{ $license['grace_period_days'] }} hari</strong>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="nexora-license-summary">
                                    <small>Nominal</small>
                                    <strong>{{ $license['payment_amount'] ? 'Rp '.number_format($license['payment_amount'], 0, ',', '.') : '-' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="nexora-license-summary">
                                    <small>User Slot</small>
                                    <strong>{{ $company->activeUsersCount() }} / {{ $company->max_users ?: '-' }}</strong>
                                </div>
                            </div>
                        </div>

                        @if($license['is_locked'])
                            <div class="nexora-billing-alert nexora-billing-alert-danger mb-3">
                                <strong>License {{ $license['label'] }}.</strong>
                                <span>{{ $company->block_reason ?: 'Hubungi Super Admin untuk mengaktifkan fitur atau memperpanjang pembayaran.' }}</span>
                            </div>
                        @elseif($license['is_warning'])
                            <div class="nexora-billing-alert nexora-billing-alert-warning mb-3">
                                <strong>{{ $license['status'] === 'GRACE_PERIOD' ? 'Grace period berjalan.' : 'Reminder pembayaran.' }}</strong>
                                <span>
                                    @if($license['status'] === 'GRACE_PERIOD')
                                        Sisa grace {{ max($license['remaining_grace_days'], 0) }} hari sebelum fitur operasional diblok.
                                    @else
                                        Sisa {{ max($license['remaining_days'], 0) }} hari sebelum jatuh tempo.
                                    @endif
                                </span>
                            </div>
                        @endif

                        <div class="row">
                            @foreach($features as $feature)
                                @php
                                    $toggle = $companyFeatures[$company->id][$feature->key] ?? null;
                                    $enabled = $toggle ? $toggle->is_enabled : true;
                                    if ($enabled && ! $blocked) {
                                        $enabledCount++;
                                    }
                                @endphp
                                <div class="col-md-4 mb-3">
                                    <div class="nexora-license-feature {{ $enabled && ! $blocked ? 'is-enabled' : 'is-disabled' }}">
                                        <div>
                                            <strong>{{ $feature->name }}</strong>
                                            <small>{{ $feature->description ?: 'Module access' }}</small>
                                        </div>
                                        <span>{{ $enabled && ! $blocked ? 'Active' : 'Off' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-muted">
                            Active features: <strong>{{ $enabledCount }}</strong> / {{ $totalFeatures }}
                            | Remaining user slots:
                            <strong>{{ is_null($company->remainingUserSlots()) ? 'Unlimited' : $company->remainingUserSlots() }}</strong>
                            @if($company->block_reason)
                                | Note: {{ $company->block_reason }}
                            @endif
                        </div>

                        @if(!auth()->user()?->isSuperAdmin() && (float) $company->payment_amount > 0)
                            <div class="mt-3">
                                <button type="button" class="btn btn-success license-qris-btn" data-company-id="{{ $company->id }}">
                                    <i class="fas fa-qrcode"></i> Bayar / Perpanjang via QRIS
                                </button>
                                <small class="text-muted ml-2">Nominal: Rp {{ number_format($company->payment_amount, 0, ',', '.') }}</small>
                            </div>
                        @endif
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
