<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - Retail Point Of sale</title>
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
            <div class="nexora-error-code">404</div>
            <h1>Halaman tidak ditemukan.</h1>
            <p>URL ini belum terdaftar di route aplikasi atau menu sudah dipindahkan.</p>

            <div class="nexora-error-actions">
                <a href="{{ url('admin-panel/dashboard') }}" class="btn btn-primary">Dashboard</a>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
            </div>
        </section>
    </main>
</body>
</html>
