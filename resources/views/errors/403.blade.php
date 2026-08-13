<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - Retail Point Of sale</title>
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/custom/css/nexora-theme.css') }}">
</head>
<body class="nexora-error-body">
    <main class="nexora-error-shell">
        <section class="nexora-error-card">
            <div class="nexora-error-brand">
                <img src="{{ asset('admin/images/retail-point-of-sale-logo.png') }}" alt="Retail Point Of sale">
                <span>Retail Point Of sale</span>
            </div>
            <div class="nexora-error-code">403</div>
            <h1>Akses halaman ini ditolak.</h1>
            <p>{{ $exception->getMessage() ?: 'Akun ini belum punya permission untuk membuka halaman tersebut.' }}</p>

            <div class="nexora-error-panel">
                <strong>Kenapa bisa muncul?</strong>
                <span>Role belum diberi permission, fitur company dimatikan, atau akun belum sesuai company.</span>
            </div>

            <div class="nexora-error-actions">
                <a href="{{ url('admin-panel/dashboard') }}" class="btn btn-primary">Dashboard</a>
                <a href="{{ route('profile.edit') }}" class="btn btn-secondary">My Profile</a>
            </div>
        </section>
    </main>
</body>
</html>
