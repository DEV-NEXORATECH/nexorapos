<x-guest-layout>
    <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
        @csrf

        @if ($errors->has('login') || $errors->has('company'))
            <div class="login-alert" role="alert">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v4M12 16h.01"></path></svg>
                <div>
                    @foreach (array_merge($errors->get('login'), $errors->get('company')) as $message)
                        <div>{{ $message }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="login-field">
            <label for="company_code">Company Code</label>
            <input id="company_code" class="login-input @error('company_code') has-error @enderror" name="company_code" type="text" value="{{ old('company_code') }}" autofocus placeholder="Enter company code" autocomplete="organization">
            <span class="login-field-help">Optional for Super Admin</span>
            @error('company_code')<span class="login-error">{{ $message }}</span>@enderror
        </div>

        @if (($loginMethod ?? 'email') === 'phone')
            <div class="login-field">
                <label for="phone">Phone Number</label>
                <input id="phone" class="login-input @error('phone') has-error @enderror" name="phone" type="text" value="{{ old('phone') }}" required placeholder="Enter your phone number" autocomplete="tel">
                @error('phone')<span class="login-error">{{ $message }}</span>@enderror
            </div>
        @else
            <div class="login-field">
                <label for="email">Email Address</label>
                <input id="email" class="login-input @error('email') has-error @enderror" name="email" type="email" value="{{ old('email') }}" required placeholder="Enter your email" autocomplete="email">
                @error('email')<span class="login-error">{{ $message }}</span>@enderror
            </div>
        @endif

        <div class="login-field">
            <label for="password">Password</label>
            <div class="login-input-wrap">
                <input id="password" class="login-input login-password-input @error('password') has-error @enderror" name="password" type="password" required placeholder="Enter your password" autocomplete="current-password">
                <button type="button" class="login-toggle-password" id="toggle-password" aria-label="Show password" aria-pressed="false">
                    <svg id="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg>
                    <svg id="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="display:none"><path d="m3 3 18 18M10.6 5.2A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-3.1 3.8M6.2 6.2C3.5 8 2 12 2 12s3.5 7 10 7a10 10 0 0 0 4-.8"></path><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"></path></svg>
                </button>
            </div>
            @error('password')<span class="login-error">{{ $message }}</span>@enderror
        </div>

        <div class="login-options">
            <label for="remember_me" class="login-remember">
                <input id="remember_me" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="login-forgot">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="login-submit" id="login-submit">
            <span id="login-submit-label">Sign In</span>
            <span class="login-spinner" id="login-spinner" aria-hidden="true" hidden></span>
        </button>
    </form>

    <script>
        (() => {
            const form = document.getElementById('login-form');
            const submit = document.getElementById('login-submit');
            const label = document.getElementById('login-submit-label');
            const spinner = document.getElementById('login-spinner');
            const password = document.getElementById('password');
            const toggle = document.getElementById('toggle-password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            toggle?.addEventListener('click', () => {
                const visible = password.type === 'text';
                password.type = visible ? 'password' : 'text';
                toggle.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
                toggle.setAttribute('aria-pressed', String(!visible));
                eyeOpen.style.display = visible ? '' : 'none';
                eyeClosed.style.display = visible ? 'none' : '';
            });

            form?.addEventListener('submit', () => {
                submit.disabled = true;
                label.textContent = 'Signing in...';
                spinner.hidden = false;
            });
        })();
    </script>
</x-guest-layout>
