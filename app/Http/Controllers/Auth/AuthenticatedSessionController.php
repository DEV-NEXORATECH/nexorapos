<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Setting;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
  public function store(LoginRequest $request): RedirectResponse
{
    \Log::info('Login attempt:', $request->all());

    $loginMethod = Setting::where('key', 'login_method')->value('value') ?? 'email';

    if ($loginMethod === 'phone') {
        $credentials = ['phone' => $request->input('phone'), 'password' => $request->input('password')];
    } else {
        $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];
    }

    \Log::info('Credentials:', $credentials);

    if (Auth::attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('admin-panel/dashboard');
    }

    \Log::warning('Authentication failed for credentials:', $credentials);

    return back()->withErrors([
        'login' => __('auth.failed'),
    ]);
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
