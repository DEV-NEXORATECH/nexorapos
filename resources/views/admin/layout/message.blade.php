<!-- Floating Toast Notification System (Job-Finance style) -->
<div class="app-toast-wrap" aria-live="polite" aria-atomic="true">
    @if(session('success'))
        <div class="app-toast success" role="status">
            <span class="app-toast-icon">✓</span>
            <div>
                <strong>Berhasil</strong>
                <p>{{ session('success') }}</p>
            </div>
            <button class="app-toast-close" type="button" aria-label="Tutup">&times;</button>
        </div>
    @endif

    @if(session('error') || session('failed'))
        <div class="app-toast error" role="alert">
            <span class="app-toast-icon">!</span>
            <div>
                <strong>Gagal</strong>
                <p>{{ session('error') ?? session('failed') }}</p>
            </div>
            <button class="app-toast-close" type="button" aria-label="Tutup">&times;</button>
        </div>
    @endif

    @if(session('warning'))
        <div class="app-toast warning" role="alert">
            <span class="app-toast-icon">⚠</span>
            <div>
                <strong>Perhatian</strong>
                <p>{{ session('warning') }}</p>
            </div>
            <button class="app-toast-close" type="button" aria-label="Tutup">&times;</button>
        </div>
    @endif

    @if(session('status'))
        <div class="app-toast info" role="status">
            <span class="app-toast-icon">ℹ</span>
            <div>
                <strong>Info</strong>
                <p>{{ session('status') }}</p>
            </div>
            <button class="app-toast-close" type="button" aria-label="Tutup">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="app-toast error" role="alert">
            <span class="app-toast-icon">!</span>
            <div>
                <strong>Data belum lengkap</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button class="app-toast-close" type="button" aria-label="Tutup">&times;</button>
        </div>
    @endif
</div>
