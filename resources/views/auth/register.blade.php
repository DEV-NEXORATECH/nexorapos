<x-guest-layout>
    <div>
        <span class="inline-flex bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700">Access by admin</span>
        <h2 class="mt-4 text-3xl font-bold text-slate-900">Registrasi publik dinonaktifkan</h2>
        <p class="mt-3 text-sm leading-6 text-slate-500">
            Akun dan akses company dibuat oleh Super Admin. Minta admin membuat user, memilih company, memilih role, lalu memberikan password awal.
        </p>

        <div class="mt-8">
            <a href="{{ route('login') }}" class="inline-flex items-center border border-blue-600 bg-blue-600 px-8 py-4 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Back to login
            </a>
        </div>
    </div>
</x-guest-layout>
