@extends('admin.layout.app')

@section('title', 'Company Features - SaaS Control Center')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            @php
                $activeCompanies = $companies->filter(fn ($company) => $company->licenseStatus() === 'ACTIVE')->count();
                $blockedCompanies = $companies->filter(fn ($company) => $company->isLicenseLocked())->count();
                $dueSoonCompanies = $companies->filter(fn ($company) => $company->isPaymentWarning())->count();
                $activePercent = $companies->count() > 0 ? round(($activeCompanies / $companies->count()) * 100) : 0;
            @endphp

            {{-- 1. HERO BANNER (JOB-FINANCE STYLE) --}}
            <div class="dashboard-hero-banner">
                <div class="dashboard-hero-left">
                    <div class="dashboard-hero-badges">
                        <span class="hero-tag-pill">
                            <i class="fa fa-circle" style="font-size: 6px; color: #10B981;"></i>
                            SAAS CONTROL CENTER
                        </span>
                        <span class="hero-tag-pill">
                            <i class="fa fa-building"></i>
                            {{ $companies->count() }} TENANT PLATFORM
                        </span>
                    </div>
                    <h2>Kontrol Fitur & Lisensi Perusahaan.</h2>
                    <p>Pusat kendali Super Admin untuk membuat tenant baru, pantau masa aktif lisensi, kelola billing pembayaran, dan konfigurasi modul fitur setiap company secara real-time.</p>
                    <a href="{{ url('admin-panel/features/create') }}" class="dashboard-hero-btn">
                        <i class="fa fa-plus-circle mr-1"></i> Tambah Perusahaan Baru &rarr;
                    </a>
                </div>
                <div class="dashboard-hero-right d-none d-lg-flex">
                    <div class="hero-stat-card-preview">
                        <div class="hero-stat-badge-check">✓</div>
                        <small>Kesehatan Lisensi SaaS</small>
                        <strong>{{ $activePercent }}% Aktif</strong>
                        <div class="hero-bars-sim">
                            <div class="hero-bar-col" style="height: 45%;"></div>
                            <div class="hero-bar-col" style="height: 65%;"></div>
                            <div class="hero-bar-col" style="height: 55%;"></div>
                            <div class="hero-bar-col" style="height: 80%;"></div>
                            <div class="hero-bar-col" style="height: 70%;"></div>
                            <div class="hero-bar-col" style="height: 95%; background: #10B981;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ALERTS --}}
            @if(!empty($setupWarnings))
                <div class="alert alert-warning d-flex align-items-center mb-4" style="border-radius: 12px; border: 1px solid #FDE68A; background: #FFFBEB; color: #92400E; padding: 16px 20px;">
                    <i class="fa fa-exclamation-triangle fa-2x mr-3 text-warning"></i>
                    <div>
                        <strong class="d-block mb-1" style="font-size: 14px;">Setup License Belum Lengkap</strong>
                        <span style="font-size: 13px;">{{ implode(' ', $setupWarnings) }} Setelah migration berjalan, fitur billing dan modul lisensi dapat digunakan penuh.</span>
                    </div>
                </div>
            @endif

            @if(session('created_company_code'))
                <div class="alert alert-success d-flex align-items-center mb-4" style="border-radius: 12px; border: 1px solid #A7F3D0; background: #ECFDF5; color: #065F46; padding: 18px 22px;">
                    <i class="fa fa-check-circle fa-2x mr-3 text-success"></i>
                    <div>
                        <strong class="d-block mb-1" style="font-size: 15px;">Perusahaan Berhasil Dibuat!</strong>
                        <p class="mb-1" style="font-size: 13px;">Company Code: <span class="badge badge-success px-2 py-1" style="font-size: 13px; letter-spacing: 0.5px;">{{ session('created_company_code') }}</span> (Berikan code ini ke admin company untuk login pertama kali).</p>
                        @if(session('created_company_domain'))
                            <p class="mb-0" style="font-size: 13px;">URL Toko POS: <a href="{{ session('created_company_domain') }}" target="_blank" rel="noopener" class="text-success font-weight-bold">{{ session('created_company_domain') }} &rarr;</a></p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 2. METRIC OVERVIEW CARDS (EXACT DASHBOARD STYLE) --}}
            <div class="mb-4">
                <div class="mb-2">
                    <span class="eyebrow-tag"><span class="dot"></span> PLATFORM MASTER OVERVIEW</span>
                </div>
                <div class="platform-overview-grid" style="grid-template-columns: repeat(4, minmax(0, 1fr)) !important;">
                    <div class="metric-card-item">
                        <div class="metric-card-top">
                            <span class="metric-card-label">Total Perusahaan</span>
                            <span class="metric-card-icon blue"><i class="fa fa-building"></i></span>
                        </div>
                        <div class="metric-card-value">{{ $companies->count() }}</div>
                        <span class="metric-card-footer">Tenant Terdaftar</span>
                    </div>

                    <div class="metric-card-item">
                        <div class="metric-card-top">
                            <span class="metric-card-label">Lisensi Aktif</span>
                            <span class="metric-card-icon emerald"><i class="fa fa-check-circle"></i></span>
                        </div>
                        <div class="metric-card-value">{{ $activeCompanies }}</div>
                        <span class="metric-card-footer">Operasional Berjalan</span>
                    </div>

                    <div class="metric-card-item">
                        <div class="metric-card-top">
                            <span class="metric-card-label">Peringatan Pembayaran</span>
                            <span class="metric-card-icon amber"><i class="fa fa-clock"></i></span>
                        </div>
                        <div class="metric-card-value">{{ $dueSoonCompanies }}</div>
                        <span class="metric-card-footer">Perlu Perpanjangan</span>
                    </div>

                    <div class="metric-card-item">
                        <div class="metric-card-top">
                            <span class="metric-card-label">Akses Terkunci</span>
                            <span class="metric-card-icon purple"><i class="fa fa-ban"></i></span>
                        </div>
                        <div class="metric-card-value">{{ $blockedCompanies }}</div>
                        <span class="metric-card-footer">Tindakan Diperlukan</span>
                    </div>
                </div>
            </div>

            {{-- 3. COMPANY CARDS SECTION --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> DAFTAR TENANT AKTIF</span>
                        <h4 class="card-title mb-0" style="font-size: 18px; font-weight: 700; color: #0F1F3D;">Manajemen Lisensi & Modul Perusahaan</h4>
                        <p class="card-category" style="margin-bottom: 0;">Konfigurasi kuota pengguna, masa tenggang, modul POS, dan sinkronisasi domain per tenant.</p>
                    </div>
                    <a href="{{ url('admin-panel/companies') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px; font-weight: 600; padding: 6px 14px;">
                        <i class="fa fa-list mr-1"></i> Kelola Perusahaan &rarr;
                    </a>
                </div>

                @if($features->isEmpty())
                    <div class="alert alert-info d-flex align-items-center mb-4" style="border-radius: 12px; border: 1px solid #BAE6FD; background: #F0F9FF; color: #075985;">
                        <i class="fa fa-info-circle fa-2x mr-3 text-info"></i>
                        <div>
                            <strong>Belum ada modul fitur terdaftar di database.</strong>
                            <p class="mb-0" style="font-size: 13px;">Jalankan database migration untuk mengisi katalog modul POS yang bisa diatur per tenant.</p>
                        </div>
                    </div>
                @endif

                <div class="features-page-grid">
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

                        <div class="tenant-feature-card {{ $cardStatusClass }}">
                            <div>
                                {{-- Card Header: Avatar + Title + Actions --}}
                                <div class="tenant-card-header">
                                    <div class="tenant-header-info">
                                        <div class="tenant-avatar-icon">
                                            <i class="fa fa-store"></i>
                                        </div>
                                        <div class="tenant-title-wrap">
                                            <h5 class="tenant-name" title="{{ $company->name }}">{{ $company->name }}</h5>
                                            <span class="tenant-code-badge">{{ $company->company_code }}</span>
                                        </div>
                                    </div>
                                    <div class="tenant-header-actions">
                                        <a href="{{ url('admin-panel/companies/show', $company->id) }}" class="btn-tenant-action" title="Detail Perusahaan">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ url('admin-panel/companies/edit', $company->id) }}" class="btn-tenant-action" title="Edit Profil Perusahaan">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                    </div>
                                </div>

                                {{-- Status Badges Row --}}
                                <div class="tenant-status-row">
                                    @if($license['status'] === 'ACTIVE')
                                        <span class="tenant-status-pill is-active">
                                            <i class="fa fa-circle" style="font-size: 6px; color: #10B981;"></i> {{ $billingLabel }}
                                        </span>
                                    @elseif(in_array($license['status'], ['EXPIRING_SOON', 'GRACE_PERIOD']))
                                        <span class="tenant-status-pill is-warning">
                                            <i class="fa fa-clock"></i> {{ $billingLabel }}
                                        </span>
                                    @else
                                        <span class="tenant-status-pill is-danger">
                                            <i class="fa fa-lock"></i> {{ $billingLabel }}
                                        </span>
                                    @endif

                                    <span class="tenant-status-pill {{ $licenseInactive ? 'is-danger' : 'is-info' }}">
                                        <i class="fa {{ $licenseInactive ? 'fa-pause' : 'fa-cash-register' }}"></i>
                                        {{ $licenseInactive ? 'Akses Dibatasi' : 'Operasional POS' }}
                                    </span>
                                </div>

                                {{-- Domain Toko Bar --}}
                                <div class="tenant-domain-bar">
                                    <div class="tenant-domain-left">
                                        <i class="fa fa-globe text-primary"></i>
                                        @if($company->subdomain)
                                            <a href="https://{{ $company->subdomain }}.{{ config('services.hostinger.domain') }}" target="_blank" rel="noopener" class="tenant-domain-link">
                                                {{ $company->subdomain }}.{{ config('services.hostinger.domain') }}
                                            </a>
                                        @else
                                            <span>Domain toko belum dibuat</span>
                                        @endif
                                    </div>

                                    <div>
                                        @if($company->subdomain)
                                            <span class="tenant-status-pill {{ $company->domain_status === 'active' ? 'is-active' : ($company->domain_status === 'failed' ? 'is-danger' : 'is-warning') }}" style="font-size: 10px; padding: 2px 7px;">
                                                {{ strtoupper($company->domain_status ?: 'Active') }}
                                            </span>
                                        @endif

                                        @if($company->domain_status !== 'active')
                                            <form method="POST" action="{{ url('admin-panel/features/'.$company->id.'/domain/retry') }}" class="d-inline ml-1">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-primary" style="border-radius: 6px; font-size: 11px; padding: 2px 8px; font-weight: 600;">
                                                    Buat Domain
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                {{-- Inset 2x2 Metrics --}}
                                <div class="tenant-metrics-inset">
                                    <div class="tenant-metric-box">
                                        <span class="tenant-metric-label">Modul Aktif</span>
                                        <strong class="tenant-metric-value">{{ $enabledFeatureCount }} / {{ $features->count() }}</strong>
                                    </div>
                                    <div class="tenant-metric-box">
                                        <span class="tenant-metric-label">Jatuh Tempo</span>
                                        <strong class="tenant-metric-value">{{ optional($license['expiry_date'])->format('d M Y') ?? '-' }}</strong>
                                    </div>
                                    <div class="tenant-metric-box">
                                        <span class="tenant-metric-label">Batas User</span>
                                        <strong class="tenant-metric-value">{{ $company->activeUsersCount() }} / {{ $company->max_users ?: 'Unlimited' }}</strong>
                                    </div>
                                    <div class="tenant-metric-box">
                                        <span class="tenant-metric-label">Sisa Waktu</span>
                                        <strong class="tenant-metric-value {{ $licenseInactive ? 'text-danger' : ($daysRemaining <= 7 ? 'text-warning' : 'text-success') }}">
                                            @if(is_null($daysRemaining))
                                                -
                                            @elseif($license['status'] === 'GRACE_PERIOD')
                                                Grace {{ max($license['remaining_grace_days'], 0) }} hari
                                            @elseif($license['is_locked'])
                                                Terkunci
                                            @else
                                                {{ $daysRemaining }} hari
                                            @endif
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Manage Action Button --}}
                            <div>
                                <a href="{{ url('admin-panel/features/'.$company->id.'/manage') }}" class="btn-tenant-manage">
                                    <i class="fa fa-sliders-h mr-1"></i> Kelola Fitur & Lisensi &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    @if(!empty($setupWarnings))
        if (window.NexoraPopup) {
            window.NexoraPopup.error({
                title: 'Setup License Belum Lengkap',
                message: @json(implode(' ', $setupWarnings)),
                detail: 'Ini penyebab fitur billing bisa memunculkan 500. Jalankan migration terbaru agar kolom dan tabel license tersedia.',
            });
        }
    @endif
</script>
@endpush
