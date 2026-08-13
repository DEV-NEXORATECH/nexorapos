<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Server Error - Retail Point Of sale</title>
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
            <div class="nexora-error-code">500</div>
            <h1>Server lagi gagal memproses halaman ini.</h1>
            <p>
                Biasanya karena migration belum lengkap, koneksi database bermasalah, atau data yang dibutuhkan fitur ini belum ada.
                Sistem tidak menampilkan error mentah supaya user tidak bingung.
            </p>

            <div class="nexora-error-panel">
                <strong>Yang perlu dicek admin</strong>
                <span>Storage log Laravel, koneksi database, tabel/kolom migration terbaru, dan permission route halaman ini.</span>
                @if(config('app.debug') && isset($exception) && $exception->getMessage())
                    <small>{{ $exception->getMessage() }}</small>
                @endif
            </div>

            <div class="nexora-error-actions">
                <a href="{{ url('admin-panel/dashboard') }}" class="btn btn-primary">Dashboard</a>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
            </div>
        </section>
    </main>
</body>
</html>
