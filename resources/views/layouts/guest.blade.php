<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Retail Point Of Sale') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('admin/custom/css/nexora-strict-override.css') }}">
        <style>
            :root { --login-navy: #0B2345; --login-dark-navy: #123A6B; --login-bg: #F8FAFC; --login-text: #0F172A; --login-muted: #475569; --login-border: #D9E2EC; --login-focus: #2563EB; --login-error: #DC2626; }
            .login-shell { min-height: 100vh; display: grid; grid-template-columns: 45% 55%; background: var(--login-bg); color: var(--login-text); font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
            .login-form-panel { display: flex; align-items: center; justify-content: center; background: #fff; padding: 48px clamp(24px, 6vw, 88px); }
            .login-form-wrap { width: min(100%, 420px); animation: login-fade-in .22s ease-out both; }
            .login-brand { display: inline-flex; align-items: center; gap: 12px; margin-bottom: 42px; color: var(--login-navy); text-decoration: none; }
            .login-brand img { width: 42px; height: 42px; object-fit: contain; }
            .login-brand span { font-size: 19px; font-weight: 800; letter-spacing: -.02em; }
            .login-heading { margin: 0; font-size: clamp(28px, 3vw, 36px); line-height: 1.15; letter-spacing: -.04em; font-weight: 800; color: var(--login-text); }
            .login-subheading { margin: 10px 0 34px; color: var(--login-muted); font-size: 15px; line-height: 1.6; }
            .login-alert { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 22px; padding: 12px 14px; border: 1px solid #fecaca; border-radius: 8px; background: #fef2f2; color: var(--login-error); font-size: 13px; line-height: 1.5; }
            .login-field { margin-bottom: 20px; }
            .login-field label { display: block; margin-bottom: 8px; color: var(--login-text); font-size: 13px; font-weight: 700; }
            .login-field-help { display: block; margin-top: 6px; color: #64748b; font-size: 12px; }
            .login-input-wrap { position: relative; }
            .login-input { display: block; width: 100%; height: 48px; padding: 0 14px; border: 1px solid var(--login-border); border-radius: 8px; background: #fff; color: var(--login-text); font-size: 14px; outline: none; transition: border-color .18s ease, box-shadow .18s ease; }
            .login-input::placeholder { color: #94a3b8; }
            .login-input:focus { border-color: var(--login-focus); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
            .login-input.has-error { border-color: var(--login-error); }
            .login-password-input { padding-right: 48px; }
            .login-toggle-password { position: absolute; top: 50%; right: 12px; display: grid; width: 30px; height: 30px; place-items: center; transform: translateY(-50%); border: 0; background: transparent; color: #64748b; cursor: pointer; }
            .login-toggle-password:hover { color: var(--login-navy); }
            .login-error { display: block; margin-top: 7px; color: var(--login-error); font-size: 12px; line-height: 1.4; }
            .login-options { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin: 2px 0 26px; }
            .login-remember { display: inline-flex; align-items: center; gap: 8px; color: var(--login-muted); font-size: 13px; cursor: pointer; }
            .login-remember input { width: 16px; height: 16px; accent-color: var(--login-navy); }
            .login-forgot { color: var(--login-navy); font-size: 13px; font-weight: 700; text-decoration: none; }
            .login-forgot:hover { color: var(--login-dark-navy); text-decoration: underline; }
            .login-submit { display: inline-flex; width: 100%; height: 48px; align-items: center; justify-content: center; gap: 9px; border: 0; border-radius: 8px; background: var(--login-navy); color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; transition: background-color .18s ease, transform .18s ease; }
            .login-submit:hover { background: var(--login-dark-navy); }
            .login-submit:focus-visible { outline: 3px solid rgba(37, 99, 235, .25); outline-offset: 3px; }
            .login-submit:disabled { cursor: wait; opacity: .78; }
            .login-spinner { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.35); border-top-color: #fff; border-radius: 50%; animation: login-spin .7s linear infinite; }
            .login-powered { display: flex; align-items: center; gap: 10px; margin-top: 56px; color: #64748b; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
            .login-powered img { width: auto; height: 24px; object-fit: contain; }
            .login-hero { position: relative; min-height: 100vh; overflow: hidden; background: var(--login-navy); }
            .login-hero::before { position: absolute; inset: 0; z-index: 1; background: linear-gradient(90deg, rgba(11,35,69,.66) 0%, rgba(11,35,69,.18) 52%, rgba(11,35,69,.4) 100%); content: ""; }
            .login-hero img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
            .login-hero-copy { position: absolute; z-index: 2; right: clamp(24px, 7vw, 100px); bottom: clamp(34px, 8vw, 100px); left: clamp(24px, 7vw, 100px); color: #fff; }
            .login-hero-copy::before { display: block; width: 42px; height: 3px; margin-bottom: 18px; border-radius: 2px; background: #fff; content: ""; }
            .login-hero-copy h2 { margin: 0 0 12px; color: #fff; font-size: clamp(28px, 4vw, 48px); line-height: 1.08; letter-spacing: -.045em; font-weight: 800; }
            .login-hero-copy p { max-width: 390px; margin: 0; color: rgba(255,255,255,.84); font-size: 15px; line-height: 1.65; }
            @keyframes login-fade-in { from { opacity: 0; transform: translateY(7px); } to { opacity: 1; transform: translateY(0); } }
            @keyframes login-spin { to { transform: rotate(360deg); } }
            @media (max-width: 1023px) { .login-shell { grid-template-columns: 55% 45%; } .login-form-panel { padding-inline: 36px; } .login-hero-copy { right: 30px; bottom: 48px; left: 30px; } .login-hero-copy h2 { font-size: 34px; } }
            @media (max-width: 767px) { .login-shell { display: block; min-height: 100svh; } .login-form-panel { min-height: 100svh; padding: 32px 24px; } .login-brand { margin-bottom: 48px; } .login-powered { margin-top: 48px; } .login-hero { display: none; } }
            @media (prefers-reduced-motion: reduce) { .login-form-wrap, .login-spinner { animation: none; } .login-input, .login-submit { transition: none; } }
        </style>
    </head>
    <body>
        <div class="login-shell">
            <section class="login-form-panel" aria-label="Login form">
                <div class="login-form-wrap">
                    <a href="{{ url('/') }}" class="login-brand" aria-label="Retail Point Of Sale home">
                        <img src="{{ asset('admin/images/retail-point-of-sale-logo.png') }}" alt="">
                        <span>Retail Point Of Sale</span>
                    </a>
                    <h1 class="login-heading">Welcome Back</h1>
                    <p class="login-subheading">Sign in to your Retail POS workspace</p>
                    {{ $slot }}
                    <div class="login-powered">
                        <span>Powered by</span>
                        <img src="{{ asset('admin/images/nexora-technology-logo.png') }}" alt="Nexora Technology">
                    </div>
                </div>
            </section>
            <aside class="login-hero" aria-label="Retail POS workspace">
                <img src="{{ asset('admin/images/retail-pos-login-hero.png') }}" alt="Modern retail point of sale checkout counter">
                <div class="login-hero-copy">
                    <h2>Retail Made Simple.</h2>
                    <p>Manage sales, inventory, and daily operations in one workspace.</p>
                </div>
            </aside>
        </div>
    </body>
</html>
