<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\MobileApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (! Schema::hasTable('mobile_api_tokens')) {
            return $this->json(false, 'Migration mobile_api_tokens belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        $data = $request->validate([
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'password' => ['required', 'string'],
            'company_code' => ['nullable', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        if (empty($data['email']) && empty($data['phone'])) {
            return $this->json(false, 'Email atau phone wajib diisi.', null, 422);
        }

        $query = User::withoutGlobalScopes()->with(['company', 'role', 'store']);

        if (! empty($data['email'])) {
            $query->where('email', $data['email']);
        } else {
            $query->where('phone', $data['phone']);
        }

        if (! empty($data['company_code'])) {
            $company = Company::where('company_code', $data['company_code'])->first();
            if (! $company) {
                return $this->json(false, 'Company code tidak ditemukan.', null, 422);
            }
            $query->where('company_id', $company->id);
        } else {
            $query->where('is_super_admin', true);
        }

        $user = $query->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->json(false, 'Credential tidak valid.', null, 401);
        }

        if (! $user->isSuperAdmin() && ! $user->company) {
            return $this->json(false, 'Akun belum terhubung ke company.', null, 403);
        }

        $plainToken = Str::random(80);
        $refreshToken = Str::random(100);
        $token = MobileApiToken::create([
            'user_id' => $user->id,
            'name' => $data['device_name'] ?? 'mobile',
            'token_hash' => hash('sha256', $plainToken),
            'refresh_token_hash' => Schema::hasColumn('mobile_api_tokens', 'refresh_token_hash') ? hash('sha256', $refreshToken) : null,
            'abilities' => ['*'],
            'expires_at' => now()->addDays(30),
            'refresh_expires_at' => Schema::hasColumn('mobile_api_tokens', 'refresh_expires_at') ? now()->addDays(60) : null,
        ]);

        return $this->json(true, 'Login berhasil.', [
            'token_type' => 'Bearer',
            'access_token' => $plainToken,
            'refresh_token' => Schema::hasColumn('mobile_api_tokens', 'refresh_token_hash') ? $refreshToken : null,
            'expires_at' => $token->expires_at,
            'refresh_expires_at' => $token->refresh_expires_at,
            'user' => $this->userPayload($user),
        ]);
    }

    public function refresh(Request $request)
    {
        $data = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        if (! Schema::hasColumn('mobile_api_tokens', 'refresh_token_hash')) {
            return $this->json(false, 'Migration refresh token belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        $token = MobileApiToken::with('user.company', 'user.role', 'user.store')
            ->where('refresh_token_hash', hash('sha256', $data['refresh_token']))
            ->first();

        if (! $token || ! $token->user || ($token->refresh_expires_at && $token->refresh_expires_at->isPast())) {
            return $this->json(false, 'Refresh token tidak valid atau expired.', null, 401);
        }

        $plainToken = Str::random(80);
        $refreshToken = Str::random(100);
        $token->forceFill([
            'token_hash' => hash('sha256', $plainToken),
            'refresh_token_hash' => hash('sha256', $refreshToken),
            'expires_at' => now()->addDays(30),
            'refresh_expires_at' => now()->addDays(60),
        ])->save();

        return $this->json(true, 'Token berhasil direfresh.', [
            'token_type' => 'Bearer',
            'access_token' => $plainToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $token->expires_at,
            'refresh_expires_at' => $token->refresh_expires_at,
            'user' => $this->userPayload($token->user),
        ]);
    }

    public function me(Request $request)
    {
        return $this->json(true, 'Profile aktif.', [
            'user' => $this->userPayload($request->user()),
            'license' => $request->user()?->company?->licenseSnapshot(),
        ]);
    }

    public function logout(Request $request)
    {
        if ($token = $request->bearerToken()) {
            MobileApiToken::where('token_hash', hash('sha256', $token))->delete();
        }

        return $this->json(true, 'Logout berhasil.');
    }

    public function devices(Request $request)
    {
        $devices = MobileApiToken::where('user_id', $request->user()->id)
            ->latest('last_used_at')
            ->get(['id', 'name', 'last_used_at', 'expires_at', 'refresh_expires_at', 'created_at']);

        return $this->json(true, 'Device token list.', ['devices' => $devices]);
    }

    public function revokeDevice(Request $request, int $id)
    {
        $deleted = MobileApiToken::where('user_id', $request->user()->id)->whereKey($id)->delete();

        return $deleted
            ? $this->json(true, 'Device berhasil direvoke.')
            : $this->json(false, 'Device token tidak ditemukan.', null, 404);
    }

    private function userPayload(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'is_super_admin' => (bool) $user->is_super_admin,
            'capabilities' => ['checkout_atomic_rejection' => true, 'checkout_idempotency' => Schema::hasColumn('sales', 'idempotency_key') && Schema::hasIndex('sales', ['company_id', 'idempotency_key'], 'unique'), 'qris_recovery' => Schema::hasTable('company_payments') && Schema::hasIndex('company_payments', ['order_id'], 'unique') && Schema::hasColumn('payments', 'status'), 'held_merge' => Schema::hasTable('held_bills')],
            'permissions' => app(\App\Services\MobileAccess::class)->permissions($user),
            'active_shift' => \Illuminate\Support\Facades\Schema::hasTable('cashier_shifts')
                ? \App\Models\Pos\CashierShift::with('store')->where('company_id', $user->company_id)->where('user_id', $user->id)->where('status', 'open')->latest('opened_at')->first() : null,
            'require_active_shift' => true,
            'company' => $user->company ? [
                'id' => $user->company->id,
                'name' => $user->company->name,
                'company_code' => $user->company->company_code,
            ] : null,
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->role,
            ] : null,
            'store' => $user->store ? [
                'id' => $user->store->id,
                'name' => $user->store->name,
                'code' => $user->store->code,
            ] : null,
            'shift' => [
                'name' => $user->shift_name,
                'start' => $user->shift_start,
                'end' => $user->shift_end,
            ],
        ];
    }

    private function json(bool $success, string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(compact('success', 'message', 'data'), $status);
    }
}
