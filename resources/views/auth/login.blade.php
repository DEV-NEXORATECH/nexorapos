<x-guest-layout>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            <x-input-error :messages="$errors->get('login')" class="mt-3" />
            <x-input-error :messages="$errors->get('company')" class="mt-3" />
        </div>

        <div class="grid gap-4 md:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] md:items-start">
            <div>
                <x-input-label for="company_code" :value="__('Company Code')" class="text-slate-700" />
                <x-text-input id="company_code" class="block mt-1 w-full border-blue-100 bg-blue-50/40 px-3 py-3 focus:border-blue-500 focus:ring-blue-500" name="company_code" type="text" :value="old('company_code')" autofocus placeholder="Kosongkan khusus Super Admin" />
                <x-input-error :messages="$errors->get('company_code')" class="mt-2" />
            </div>

            <div>
                @if (($loginMethod ?? 'email') === 'phone')
                    <div>
                        <x-input-label for="phone" :value="__('Phone Number')" class="text-slate-700" />
                        <x-text-input id="phone" class="block mt-1 w-full border-blue-100 bg-blue-50/40 px-3 py-3 focus:border-blue-500 focus:ring-blue-500" name="phone" type="text" :value="old('phone')" required placeholder="0881023806530" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                @else
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-slate-700" />
                        <x-text-input id="email" class="block mt-1 w-full border-blue-100 bg-blue-50/40 px-3 py-3 focus:border-blue-500 focus:ring-blue-500" name="email" type="email" :value="old('email')" required placeholder="admin@company.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                @endif

                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="text-slate-700" />
                    <x-text-input id="password" class="block mt-1 w-full border-blue-100 bg-blue-50/40 px-3 py-3 focus:border-blue-500 focus:ring-blue-500" name="password" type="password" required placeholder="Password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-blue-200 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">{{ __('Forgot password?') }}</a>
                    @endif
                    <button type="submit" class="ms-3 inline-flex items-center border border-blue-600 bg-blue-600 px-6 py-3 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ __('Log in') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-5 border border-blue-100 bg-blue-50 px-3 py-2 text-xs text-blue-800">
            Demo: <strong>test@example.com</strong> / <strong>password</strong>
        </div>
    </form>
</x-guest-layout>
