
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
@endphp
<div class="logo-header">
    <a href="{{ url('admin-panel/dashboard') }}" class="logo d-flex align-items-center">
        <span class="brand-mark">
            <img src="{{ asset('admin/images/retail-point-of-sale-logo.png') }}" alt="Retail Point Of sale">
        </span>
        <span class="brand-name">Retail POS</span>
    </a>
    <button class="navbar-toggler sidenav-toggler sidebar-toggle-control ml-auto" type="button" aria-label="Toggle sidebar">
        <i class="la la-bars"></i>
    </button>
    <button class="topbar-toggler more"><i class="la la-ellipsis-v"></i></button>
</div>

<nav class="navbar navbar-header navbar-expand-lg">
    <div class="container-fluid">

        <div class="nexora-navbar-spacer"></div>
        <ul class="navbar-nav topbar-nav ml-md-auto align-items-center nexora-topbar-actions">

            @if($navLicense)
                <li class="nav-item">
                    <a href="{{ url('admin-panel/settings/license') }}" class="nexora-license-pill nexora-license-pill-{{ $navLicense['badge_class'] }}">
                        <i class="la la-certificate"></i>
                        <span>{{ $navLicenseText }}</span>
                    </a>
                </li>
            @endif

            <li class="nav-item">
                <a href="#" class="nexora-nav-icon" title="Notifications">
                    <i class="la la-bell"></i>
                    @if($navLicense && $navLicense['is_warning'])
                        <span class="nexora-dot"></span>
                    @endif
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                    @if($profileImage)
                        <img src="{{ $profileImage }}" alt="{{ $navUser->name }}" width="36" class="img-circle">
                    @else
                        <span class="avatar-fallback">{{ strtoupper(substr($navUser->name, 0, 1)) }}</span>
                    @endif
                    <span class="nexora-profile-name">{{ $navUser->name }}</span>
                    <i class="la la-angle-down nexora-profile-chevron"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li>
                        <div class="user-box">
                            <div class="u-img">
                                @if($profileImage)
                                    <img src="{{ $profileImage }}" alt="{{ $navUser->name }}">
                                @else
                                    <span class="avatar-fallback avatar-fallback-lg">{{ strtoupper(substr($navUser->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="u-text">
                                <h4>{{ $navUser->name }}</h4>
                                <p class="text-muted mb-1">{{ $navUser->email }}</p>
                                <p class="text-muted mb-1">{{ $navUser->role?->name ?? ($navUser->isSuperAdmin() ? 'Super Admin' : 'User') }}</p>
                                <p class="text-muted mb-2">{{ $navCompany?->company_code ?? 'Super Admin' }}</p>
                            </div>
                        </div>
                    </li>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="la la-user"></i> My Profile</a>
                    @if($navCompany && ! $navUser->isSuperAdmin())
                        <a class="dropdown-item" href="{{ url('admin-panel/settings/company') }}"><i class="la la-building"></i> Company Settings</a>
                        <a class="dropdown-item" href="{{ url('admin-panel/settings/license') }}"><i class="la la-certificate"></i> License Status</a>
                    @endif
                    <a class="dropdown-item" href="{{ route('profile.edit') }}#password"><i class="la la-lock"></i> Change Password</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"><i class="fa fa-power-off"></i> Logout</a>
                </ul>
            </li>
        </ul>
    </div>
</nav>
