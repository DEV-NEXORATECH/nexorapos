<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('admin/custom/css/nexora-strict-override.css') }}">
    </head>
    <body class="font-sans text-slate-900 antialiased bg-blue-50">
        <div class="flex min-h-screen items-center justify-center px-5 py-10">
            <main class="w-full max-w-4xl rounded-2xl border border-blue-100 bg-white px-7 py-8 shadow-xl shadow-blue-900/10 sm:px-9">
                <div class="mb-7 text-center">
                    <img src="{{ asset('admin/images/retail-point-of-sale-logo.png') }}" alt="Retail Point Of sale" class="mx-auto h-16 w-auto object-contain">
                    <h1 class="mt-4 text-2xl font-bold text-blue-700">Retail Point Of sale</h1>
                    <p class="mt-2 text-sm text-slate-500">Masuk ke workspace toko kamu.</p>
                </div>

                {{ $slot }}

                <div class="mt-8 flex items-center justify-center gap-3 border-t border-blue-100 pt-5 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                    <span>Powered by</span>
                    <img src="{{ asset('admin/images/nexora-technology-logo.png') }}" alt="Nexora Technology" class="h-8 w-auto object-contain">
                </div>
            </main>
        </div>
    </body>
</html>
