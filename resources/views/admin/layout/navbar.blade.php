@php
    $navUser = Auth::user();
    $profileImage = $navUser->profile_image
        ? asset('admin/profile_images/'.Auth::user()->profile_image)
        : null;
    $navCompany = $navUser->company;
    $navLicense = $navCompany && ! $navUser->isSuperAdmin() ? $navCompany->licenseSnapshot() : null;
    $navLicenseText = null;
    if ($navLicense) {
        $navLicenseText = match ($navLicense['status']) {
            'ACTIVE', 'EXPIRING_SOON' => is_null($navLicense['remaining_days'])
                ? $navLicense['label']
                : max($navLicense['remaining_days'], 0).' Days Left',
            'GRACE_PERIOD' => 'Grace: '.max($navLicense['remaining_grace_days'], 0).' Days',
            default => $navLicense['label'],
        };
    }
    $initial = strtoupper(substr($navUser->name ?? 'U', 0, 1));
@endphp

<div class="logo-header">
    <a href="{{ url('admin-panel/dashboard') }}" class="logo">
        <img src="{{ asset('admin/images/nexora-technology-logo.png') }}" alt="Nexora Technology" class="brand-full-logo" onerror="this.style.display='none'">
        <img src="{{ asset('admin/images/nexora-emblem.png') }}" alt="Nexora" class="brand-mini-logo" onerror="this.style.display='none'">
    </a>
</div>

<nav class="navbar navbar-header navbar-expand-lg">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        
        <!-- Left: Single Sidebar Toggle Button + Breadcrumb (Job-Finance style) -->
        <div class="topbar-left d-flex align-items-center">
            <button class="sidebar-toggle-control mr-3" type="button" aria-label="Toggle sidebar" title="Buka / Tutup Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nexora-topbar-breadcrumb d-none d-sm-flex align-items-center">
                <span class="breadcrumb-parent">{{ $navCompany ? $navCompany->name : 'Workspace' }}</span>
                <span class="divider mx-2">/</span>
                <strong class="breadcrumb-current-page">@yield('title', 'Dashboard')</strong>
            </div>
        </div>

        <!-- Right: Actions -->
        <ul class="navbar-nav topbar-nav ml-auto align-items-center nexora-topbar-actions">
            
            <!-- License Pill -->
            @if($navLicense)
                <li class="nav-item">
                    <a href="{{ url('admin-panel/settings/license') }}" class="nexora-license-pill nexora-license-pill-{{ $navLicense['badge_class'] }}" title="License {{ $navLicense['label'] }}">
                        <i class="fas fa-certificate"></i>
                        <span>{{ $navLicenseText }}</span>
                    </a>
                </li>
            @endif

            <!-- Dark / Light Mode Switch (Job-Finance style) -->
            <li class="nav-item">
                <button type="button" class="theme-toggle-btn" data-theme-toggle title="Ganti Tema (Dark / Light)" aria-label="Ganti Tema">
                    <i class="fas fa-moon" data-theme-icon></i>
                </button>
            </li>

            <!-- Notifications -->
            <li class="nav-item dropdown">
                <a href="#" class="nexora-nav-icon" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" title="Notifikasi" aria-label="Notifikasi">
                    <i class="fas fa-bell"></i>
                    @if($navLicense && $navLicense['is_warning'])
                        <span class="nexora-dot"></span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right nexora-notification-menu shadow-lg" style="min-width: 290px;">
                    <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
                        <strong class="small font-weight-bold">Notifikasi</strong>
                        <span class="badge badge-primary" style="font-size: 10px; padding: 2px 8px;">Sistem</span>
                    </div>
                    @if($navLicense)
                        <a href="{{ url('admin-panel/settings/license') }}" class="dropdown-item py-2">
                            <i class="fas fa-certificate text-primary mr-2" style="font-size: 16px;"></i>
                            <div class="d-flex flex-column">
                                <strong class="small">Status Lisensi: {{ $navLicense['label'] }}</strong>
                                <small class="text-muted">
                                    @if($navLicense['is_locked'])
                                        Fitur operasional sedang dikunci.
                                    @elseif($navLicense['is_warning'])
                                        Jatuh tempo {{ optional($navLicense['expiry_date'])->format('d M Y') ?? '-' }}.
                                    @else
                                        Lisensi aktif hingga {{ optional($navLicense['expiry_date'])->format('d M Y') ?? '-' }}.
                                    @endif
                                </small>
                            </div>
                        </a>
                    @elseif($navUser->isSuperAdmin())
                        <a href="{{ url('admin-panel/features') }}" class="dropdown-item py-2">
                            <i class="fas fa-toggle-on text-primary mr-2" style="font-size: 16px;"></i>
                            <div class="d-flex flex-column">
                                <strong class="small">Company & Fitur</strong>
                                <small class="text-muted">Kelola fitur dan lisensi semua tenant.</small>
                            </div>
                        </a>
                    @else
                        <div class="dropdown-item py-3 text-muted text-center small">
                            <i class="fas fa-circle-check text-success mb-1" style="font-size: 20px; display: block;"></i>
                            Semua operasional berjalan normal.
                        </div>
                    @endif
                </div>
            </li>

            <!-- User Profile Dropdown (Job-Finance style: no border wrapper, clean avatar + name/role) -->
            <li class="nav-item dropdown">
                <a class="nexora-account-btn dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
                    @if($profileImage)
                        <img src="{{ $profileImage }}" alt="{{ $navUser->name }}" width="36" height="36" class="rounded-circle object-cover flex-shrink-0">
                    @else
                        <span class="avatar-fallback">{{ $initial }}</span>
                    @endif
                    <div class="nexora-account-info d-none d-sm-flex">
                        <span class="nexora-account-name">{{ Str::limit($navUser->name, 20) }}</span>
                        <small class="nexora-account-role">{{ $navUser->role?->role ?? ($navUser->isSuperAdmin() ? 'Super Admin' : 'User') }}</small>
                    </div>
                    <i class="fas fa-chevron-down nexora-profile-chevron ml-1"></i>
                </a>
                
                <div class="dropdown-menu dropdown-menu-right shadow-lg" style="min-width: 240px;">
                    <div class="px-3 py-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <span class="avatar-fallback mr-2">{{ $initial }}</span>
                            <div style="overflow: hidden;">
                                <h6 class="mb-0 font-weight-bold text-truncate" style="font-size: 13px;">{{ $navUser->name }}</h6>
                                <small class="text-muted text-truncate d-block" style="font-size: 11px;">{{ $navUser->email }}</small>
                            </div>
                        </div>
                    </div>

                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user-circle text-primary mr-2" style="font-size: 14px; width: 16px; text-align: center;"></i>
                        <span>Profil Saya</span>
                    </a>

                    @if($navCompany && ! $navUser->isSuperAdmin())
                        <a class="dropdown-item" href="{{ url('admin-panel/settings/company') }}">
                            <i class="fas fa-building text-primary mr-2" style="font-size: 14px; width: 16px; text-align: center;"></i>
                            <span>Pengaturan Perusahaan</span>
                        </a>
                        <a class="dropdown-item" href="{{ url('admin-panel/settings/license') }}">
                            <i class="fas fa-certificate text-primary mr-2" style="font-size: 14px; width: 16px; text-align: center;"></i>
                            <span>Status Lisensi</span>
                        </a>
                    @endif

                    <a class="dropdown-item" href="{{ route('profile.edit') }}#password">
                        <i class="fas fa-lock text-primary mr-2" style="font-size: 14px; width: 16px; text-align: center;"></i>
                        <span>Ganti Password</span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-left">
                            <i class="fas fa-arrow-right-from-bracket text-danger mr-2" style="font-size: 14px; width: 16px; text-align: center;"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </li>

        </ul>
    </div>
</nav>
