@extends('admin.layout.app')

@section('title', 'Profile')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Profile</h4>

            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="nexora-profile-summary">
                        <small>Akun</small>
                        <strong>{{ $user->name }}</strong>
                        <span>{{ $user->email }}</span>
                        <span>{{ $user->phone ?: 'Phone belum diisi' }}</span>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="nexora-profile-summary">
                        <small>Role & Company</small>
                        <strong>{{ $user->role?->name ?? 'Role belum diset' }}</strong>
                        <span>{{ $company?->name ?? 'Company belum diset' }}</span>
                        <span>Code: {{ $company?->company_code ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="nexora-profile-summary">
                        <small>License</small>
                        @if($company)
                            <strong class="{{ $company->isLicenseInactive() ? 'text-danger' : 'text-primary' }}">
                                {{ $company->isLicenseInactive() ? 'Inactive' : 'Active' }}
                            </strong>
                            <span>{{ $enabledFeatureCount }} dari {{ $totalFeatures }} fitur aktif</span>
                            <span>{{ ucfirst(str_replace('_', ' ', $company->subscription_status ?? 'active')) }}</span>
                        @else
                            <strong class="text-danger">No Company</strong>
                            <span>Minta Super Admin assign company.</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="nexora-profile-summary">
                        <small>Pembayaran</small>
                        @if($company)
                            <strong>{{ $company->billingPeriodLabel() }}</strong>
                            <span>Due: {{ optional($company->payment_due_at)->format('d M Y') ?? '-' }}</span>
                            <span>
                                Sisa:
                                @if(is_null($company->paymentDaysRemaining()))
                                    -
                                @else
                                    {{ $company->paymentDaysRemaining() }} hari
                                @endif
                            </span>
                            <span>Nominal: {{ $company->payment_amount ? 'Rp '.number_format($company->payment_amount, 0, ',', '.') : '-' }}</span>
                        @else
                            <strong>-</strong>
                            <span>Belum ada data pembayaran.</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($company && ($company->isPaymentWarning() || $company->isLicenseInactive()))
                <div class="nexora-billing-alert {{ $company->isLicenseInactive() ? 'nexora-billing-alert-danger' : 'nexora-billing-alert-warning' }} mb-4">
                    <strong>{{ $company->isLicenseInactive() ? 'License tidak aktif.' : 'Reminder pembayaran.' }}</strong>
                    <span>
                        {{ $company->block_reason ?: 'Hubungi Super Admin jika butuh perpanjangan pembayaran atau aktivasi fitur.' }}
                    </span>
                </div>
            @endif

            {{-- Update Profile Information --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Update Profile Information</div>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="card mt-4">
                <div class="card-header">
                    <div class="card-title">Update Password</div>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="card mt-4">
                <div class="card-header">
                    <div class="card-title">Delete Account</div>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
