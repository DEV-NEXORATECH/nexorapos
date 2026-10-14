<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Permission;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Redirect to login if user is not authenticated
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $currentUri = Route::current()->uri();

        if (in_array($currentUri, ['admin-panel/dashboard', 'admin-panel/business-process', 'admin-panel/settings/license'])) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if ($currentUri === 'admin-panel/sales-data') {
            return $next($request);
        }

        if (! $user->company?->hasAnyEnabledFeature()) {
            abort(403, 'License company belum aktif karena semua fitur dimatikan oleh Super Admin.');
        }

        $role = $user->role;
        if (! $role) {
            abort(403, 'Your account does not have a role assigned.');
        }

        $permission = $this->resolvePermission($role, $currentUri);

        if ($permission && $this->featureIsEnabled($user, $permission)) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this route.');
    }

    private function resolvePermission($role, string $currentUri)
    {
        $candidates = [$currentUri];

        // POS actions (cart, shift, checkout, invoice) belong to the POS
        // permission even though they use different endpoint URLs.
        if (Str::startsWith($currentUri, 'admin-panel/pos/')) {
            $candidates[] = 'admin-panel/pos/sell';
        }

        $lastSlash = strrpos($currentUri, '/');
        if ($lastSlash !== false) {
            $candidates[] = substr($currentUri, 0, $lastSlash);
        }

        return $role->permissions()
            ->with('permission_category')
            ->whereIn('route', $candidates)
            ->orderByRaw('CASE WHEN route = ? THEN 0 ELSE 1 END', [$currentUri])
            ->first();
    }

    private function featureIsEnabled($user, Permission $permission): bool
    {
        $featureKey = $permission->permission_category?->feature_key;

        if (! $featureKey) {
            return true;
        }

        return $user->company?->hasFeature($featureKey) ?? false;
    }
}
