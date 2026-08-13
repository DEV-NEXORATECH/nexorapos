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
    <body class="font-sans text-slate-900 antialiased bg-white">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-[44%_56%]">
            <aside class="flex min-h-[340px] flex-col justify-between bg-blue-600 px-8 py-8 text-white sm:px-14 lg:min-h-screen lg:px-20 lg:py-16">
                <div>
                    <div class="inline-flex">
                        <img src="{{ asset('admin/images/retail-point-of-sale-logo.png') }}" alt="Retail Point Of sale" class="h-16 w-auto object-contain" style="filter: grayscale(1) invert(1) brightness(10); mix-blend-mode: screen;">
                    </div>
                    <h1 class="mt-10 max-w-md text-4xl font-bold tracking-tight sm:text-5xl">Retail Point Of sale</h1>
                    <p class="mt-5 max-w-md text-base leading-7 text-blue-50">
                        Sistem kasir simple untuk struk customer, pencocokan pendapatan harian, dan kontrol akses toko dari admin.
                    </p>
                </div>

                <div class="mt-10 grid gap-4 text-sm text-blue-50">
                    <div class="border-l-4 border-white/70 pl-4">Company dan akses user disetting oleh Super Admin.</div>
                    <div class="border-l-4 border-white/70 pl-4">User cukup login dengan email atau nomor HP yang sudah didaftarkan.</div>
                    <div class="border-l-4 border-white/70 pl-4">Checkout langsung membuat struk dan payment record.</div>
                </div>

                <div class="mt-10 flex items-center gap-3 border-t border-blue-400 pt-6 text-xs font-semibold uppercase tracking-[0.18em] text-blue-50">
                    <span>Powered by</span>
                    <img src="{{ asset('admin/images/nexora-technology-logo.png') }}" alt="Nexora Technology" class="h-14 w-auto object-contain" style="filter: grayscale(1) invert(1) brightness(10); mix-blend-mode: screen;">
                </div>
            </aside>

            <main class="flex min-h-screen items-center justify-center px-8 py-10 sm:px-12 lg:px-20">
                <div class="w-full max-w-lg">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
