@extends('admin.layout.app')

@section('title', 'Company Features')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                @php
                    $activeCompanies = $companies->filter(fn ($company) => $company->licenseStatus() === 'ACTIVE')->count();
                    $blockedCompanies = $companies->filter(fn ($company) => $company->isLicenseLocked())->count();
                    $dueSoonCompanies = $companies->filter(fn ($company) => $company->isPaymentWarning())->count();
                @endphp

                <div class="superadmin-header-card mb-4" style="align-items: flex-start;">
                    <div>
                        <span style="color: rgba(255,255,255,0.7); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">SaaS Control Center</span>
                        <h4 class="mt-1 mb-2"><i class="fas fa-layer-group"></i> Company Feature Control</h4>
                        <p style="color: rgba(255,255,255,0.8); font-size: 14px; margin-bottom: 0;">Buat company, lihat seluruh company, lalu kelola license, pembayaran, dan fitur per company.</p>
                    </div>
                    <a href="{{ url('admin-panel/features/create') }}" class="btn mt-2" style="background-color: #FFFFFF !important; color: #0058A6 !important; border: none !important; white-space: nowrap; font-weight: 600; padding: 8px 16px; border-radius: 6px;">
                        + Create Company
                    </a>
                </div>

                @if(!empty($setupWarnings))
                    <div class="nexora-billing-alert nexora-billing-alert-warning mb-3">
                        <strong>Setup license belum lengkap.</strong>
                        <span>{{ implode(' ', $setupWarnings) }} Setelah migration jalan, halaman ini bisa dipakai penuh untuk atur billing dan fitur.</span>
                    </div>
                @endif

                @if(session('created_company_code'))
                    <div class="nexora-created-code-alert mb-3">
                        <div>
                            <span>Company berhasil dibuat</span>
                            <strong>{{ session('created_company_code') }}</strong>
                            <p>Berikan company code ini ke admin company untuk login. Code dibuat otomatis dan tidak bisa diubah.</p>
                        </div>
                    </div>
                @endif

                <div class="row nexora-feature-stats">
                    <div class="col-md-3 mb-3">
                        <div class="nexora-feature-stat is-neutral">
                            <small>Total Company</small>
                            <strong>{{ $companies->count() }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="nexora-feature-stat is-success">
                            <small>License Active</small>
                            <strong>{{ $activeCompanies }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="nexora-feature-stat is-warning">
                            <small>Payment Warning</small>
                            <strong>{{ $dueSoonCompanies }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="nexora-feature-stat is-danger">
                            <small>Blocked / Inactive</small>
                            <strong>{{ $blockedCompanies }}</strong>
                        </div>
                    </div>
                </div>

                <div class="nexora-feature-companies">
                    @if($features->isEmpty())
                        <div class="nexora-empty-state">
                            <strong>Belum ada feature module.</strong>
                            <span>Menu ini akan aktif setelah tabel features terisi dari migration. Fungsinya untuk Super Admin menyalakan atau mematikan akses module per company.</span>
                        </div>
                    @endif

                    @foreach ($companies as $company)
                        @php
                            $license = $company->licenseSnapshot();
                            $daysRemaining = $license['remaining_days'];
                            $billingBadgeClass = 'nexora-status-'.$license['badge_class'];
                            $billingLabel = $license['label'];
                            $enabledFeatureCount = $company->enabledFeaturesCount();
                            $licenseInactive = $license['is_locked'] || $company->isLicenseInactive();
                            
                            $cardStatusClass = match ($license['status']) {
                                'ACTIVE' => 'is-success',
                                'EXPIRING_SOON', 'GRACE_PERIOD' => 'is-warning',
                                'OVERDUE', 'BLOCKED' => 'is-danger',
                                default => 'is-neutral',
                            };
                        @endphp

                        <section class="nexora-company-control {{ $cardStatusClass }}">
                            <div class="nexora-company-control-head">
                                <div>
                                    <div class="nexora-company-title-row">
                                        <h5>{{ $company->name }}</h5>
                                        <span class="nexora-company-code-badge">{{ $company->company_code }}</span>
                                    </div>
                                    <div class="nexora-company-actions mb-2">
                                        <a href="{{ url('admin-panel/companies/show', $company->id) }}" class="btn btn-secondary btn-sm">View</a>
                                        <a href="{{ url('admin-panel/companies/edit', $company->id) }}" class="btn btn-secondary btn-sm">Edit Company</a>
                                    </div>
                                    <div class="nexora-company-badges">
                                        <span class="nexora-status {{ $billingBadgeClass }}">{{ $billingLabel }}</span>
                                        <span class="nexora-status {{ $licenseInactive ? 'nexora-status-danger' : 'nexora-status-success' }}">
                                            {{ $licenseInactive ? 'Access Locked' : 'Operational Access' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="nexora-company-summary">
                                    <div class="nexora-company-meta">
                                        <div>
                                            <small>Active Features</small>
                                            <strong>{{ $enabledFeatureCount }} / {{ $features->count() }}</strong>
                                        </div>
                                        <div>
                                            <small>Due Date</small>
                                            <strong>{{ optional($license['expiry_date'])->format('d M Y') ?? '-' }}</strong>
                                        </div>
                                        <div>
                                            <small>User Limit</small>
                                            <strong>{{ $company->activeUsersCount() }} / {{ $company->max_users ?: '-' }}</strong>
                                        </div>
                                        <div>
                                            <small>Remaining</small>
                                            <strong>
                                                @if(is_null($daysRemaining))
                                                    -
                                                @elseif($license['status'] === 'GRACE_PERIOD')
                                                    Grace {{ max($license['remaining_grace_days'], 0) }} days
                                                @elseif($license['is_locked'])
                                                    Overdue
                                                @else
                                                    {{ $daysRemaining }} days
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                    <a href="{{ url('admin-panel/features/'.$company->id.'/manage') }}" class="btn btn-primary btn-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        @if(!empty($setupWarnings))
            window.NexoraPopup.error({
                title: 'Setup License Belum Lengkap',
                message: @json(implode(' ', $setupWarnings)),
                detail: 'Ini penyebab fitur billing bisa memunculkan 500. Jalankan migration terbaru agar kolom dan tabel license tersedia.',
            });
        @endif

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

        document.querySelectorAll('.nexora-company-detail-toggle').forEach((button) => {
            const target = document.querySelector(button.dataset.target);

            if (!target) {
                return;
            }

            if (window.jQuery) {
                window.jQuery(target).on('shown.bs.collapse', () => {
                    button.textContent = 'Hide Details';
                });

                window.jQuery(target).on('hidden.bs.collapse', () => {
                    button.textContent = 'View Details';
                });
            }
        });
    </script>
@endpush
