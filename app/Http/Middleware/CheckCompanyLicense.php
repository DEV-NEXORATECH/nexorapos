<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyLicense
{
    private const ALLOWED_WHEN_LOCKED = [
        'admin-panel/settings/license',
        'admin-panel/settings/license/payment',
        'profile',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user->company;

        if (! $company) {
            abort(403, 'Akun belum terhubung ke company. Minta Super Admin assign company terlebih dahulu.');
        }

        if (! $company->isLicenseLocked()) {
            return $next($request);
        }

        if ($this->isAllowed($request)) {
            return $next($request);
        }

        $snapshot = $company->licenseSnapshot();
        $message = 'License company '.$company->name.' '.$snapshot['label'].'. Fitur operasional dikunci sampai license diperpanjang oleh Super Admin.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'code' => 'LICENSE_'.$snapshot['status'],
                'message' => $message,
                'license' => $snapshot,
            ], 423);
        }

        abort(423, $message);
    }

    private function isAllowed(Request $request): bool
    {
        $path = trim($request->path(), '/');

        foreach (self::ALLOWED_WHEN_LOCKED as $allowed) {
            if ($path === $allowed || str_starts_with($path, $allowed.'/')) {
                return true;
            }
        }

        return false;
    }
}
