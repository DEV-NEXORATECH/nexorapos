<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Locked</title>
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/custom/css/nexora-theme.css') }}">
</head>
<body class="nexora-error-body">
    @php
        $company = auth()->user()?->company;
        $license = $company?->licenseSnapshot();
    @endphp

    <main class="nexora-error-shell">
        <div class="nexora-error-card">
            <div class="nexora-error-brand">
                <img src="{{ asset('admin/images/retail-point-of-sale-logo.png') }}" alt="Retail Point Of sale">
                <span>Retail Point Of sale</span>
            </div>
            <div class="nexora-error-code">423 LOCKED</div>
            <h1>License {{ $license['label'] ?? 'Locked' }}</h1>
            <p>
                {{ $exception->getMessage() ?: 'Fitur operasional sedang dikunci karena license company tidak aktif.' }}
            </p>

            @if($company && $license)
                <div class="nexora-license-lock-grid">
                    <div>
                        <small>Company</small>
                        <strong>{{ $company->name }}</strong>
                    </div>
                    <div>
                        <small>Status</small>
                        <strong>{{ $license['label'] }}</strong>
                    </div>
                    <div>
                        <small>Package</small>
                        <strong>{{ $license['package'] }}</strong>
                    </div>
                    <div>
                        <small>Expiry</small>
                        <strong>{{ optional($license['expiry_date'])->format('d M Y') ?? '-' }}</strong>
                    </div>
                </div>
            @endif

            <div class="nexora-license-lock-actions">
                <a href="{{ url('admin-panel/settings/license') }}" class="btn btn-primary">Lihat License</a>
                <a href="{{ route('profile.edit') }}" class="btn btn-secondary">My Profile</a>
                <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </main>
</body>
</html>
