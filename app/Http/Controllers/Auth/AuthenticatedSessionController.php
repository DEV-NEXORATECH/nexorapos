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
        $loginMethod = Setting::cachedValue('login_method', 'email');

        return view('auth.login', compact('loginMethod'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $loginMethod = Setting::cachedValue('login_method', 'email');
        
        $credentials = [
            'password' => $request->input('password'),
        ];
        
        if ($loginMethod === 'phone') {
            $credentials['phone'] = $request->input('phone');
        } else {
            $credentials['email'] = $request->input('email');
        }

        $companyCode = trim((string) $request->input('company_code'));
        
        if ($companyCode) {
            $company = \App\Models\Company::where('company_code', $companyCode)->first();
            if (!$company) {
                return back()->withErrors(['company_code' => 'Company Code tidak ditemukan.'])->onlyInput('email', 'phone', 'company_code');
            }
            $credentials['company_id'] = $company->id;
        } else {
            // Platform Super Admin can login without tenant company code.
            $credentials['is_super_admin'] = 1;
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();
            $company = $user->company;

            if (!$user->isSuperAdmin() && !$company) {
                Auth::guard('web')->logout();
                return back()->withErrors([
                    'company' => 'Akun belum terhubung ke company.',
                ])->onlyInput('email', 'phone');
            }

            $request->session()->regenerate();
            if ($company) {
                $request->session()->put('company_id', $company->id);
            }

            if ($user->isSuperAdmin()) {
                return redirect()->intended('admin-panel/dashboard');
            }

            if ($user->role && strtolower($user->role->role) === 'kasir') {
                return redirect()->intended('admin-panel/pos/sell');
            }

            return redirect()->intended('admin-panel/dashboard');
        }

        return back()->withErrors([
            'login' => __('auth.failed'),
        ])->onlyInput('email', 'phone', 'company_code');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
