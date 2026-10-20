<?php

namespace App\Http\Middleware;

use App\Models\Pos\Store;
use App\Services\MobileAccess;
use Closure;
use Illuminate\Http\Request;

class MobileApiAuthorize
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $path = trim($request->path(), '/');
        $path = preg_replace('#^api/mobile/v1/#', '', $path);
        if (str_starts_with($path, 'auth/')) return $next($request);

        if (! $user->isSuperAdmin()) {
            abort_unless($user->company_id, 403, 'Company akun belum tersedia.');
            foreach ([$request->query('company_id'), $request->input('company_id')] as $companyId) {
                abort_if($companyId !== null && (int) $companyId !== (int) $user->company_id, 403, 'Company di luar akses akun.');
            }
            foreach ([$request->query('store_id'), $request->input('store_id')] as $storeId) {
                if ($storeId !== null) {
                    abort_unless(Store::where('company_id', $user->company_id)->whereKey($storeId)->exists(), 403, 'Toko di luar company akun.');
                    abort_if($user->store_id && (int) $storeId !== (int) $user->store_id, 403, 'Toko di luar penugasan akun.');
                }
            }
        }
        // This endpoint is intentionally available to every authenticated role.
        if ($path === 'admin/mobile-menu' || in_array($path, ['license', 'features', 'stores'], true)) return $next($request);
        $access = app(MobileAccess::class);
        if (str_starts_with($path, 'admin/')) {
            if (preg_match('#^admin/(users|roles|permissions)(?:/|$)#', $path, $matches)) {
                $segment = $matches[1] === 'users' ? 'users' : 'roles';
                $actions = match ($request->method()) {
                    'GET', 'HEAD' => ['listing'], 'POST' => ['add', 'store'],
                    'PUT', 'PATCH' => ['edit', 'update'], 'DELETE' => ['delete', 'destroy'], default => [],
                };
                $allowed = $user->isSuperAdmin();
                foreach ($actions as $action) $allowed = $allowed || in_array('admin-panel/'.$segment.'/'.$action, $access->routes($user), true);
                abort_unless($allowed, 403, 'Anda tidak memiliki izin tindakan ini.');
            } elseif (preg_match('#^admin/(company-profile|companies)(?:/|$)#', $path)) {
                abort_unless($user->isSuperAdmin() || ($path === 'admin/company-profile' && $access->allows($user, 'billing.view')), 403);
            } elseif (str_starts_with($path, 'admin/reports/')) {
                abort_unless($access->allows($user, str_contains($path, 'reconciliation') ? 'pos.reconciliation' : (str_contains($path, 'shifts') ? 'pos.shifts' : 'pos.sales.view')), 403);
            } elseif ($path === 'admin/audit-logs') {
                abort_unless($user->isSuperAdmin(), 403);
            } elseif (str_starts_with($path, 'admin/uploads/')) {
                $allowed = $user->isSuperAdmin();
                if ($path === 'admin/uploads/company-logo') $allowed = $allowed || $access->allows($user, 'billing.view');
                if (preg_match('#^admin/uploads/products/[^/]+/image$#', $path)) $allowed = $allowed || $access->resourceAllowed($user, 'products', 'PATCH');
                if (preg_match('#^admin/uploads/users/[^/]+/profile-image$#', $path)) $allowed = $allowed || $access->resourceAllowed($user, 'users', 'PATCH');
                abort_unless($allowed, 403);
            } else {
                $resource = explode('/', $path)[1] ?? '';
                abort_unless($access->resourceAllowed($user, $resource, $request->method()), 403, 'Anda tidak memiliki izin resource ini.');
            }
            return $next($request);
        }
        if ($request->isMethod('GET') && preg_match('#^pos/sales/[0-9]+$#', $path)) {
            abort_unless($access->allows($user, 'pos.sales.view') || $access->allows($user, 'pos.checkout'), 403);
            $request->attributes->set('mobile_own_sale_only', ! $access->allows($user, 'pos.sales.view'));
            return $next($request);
        }
        $permission = match (true) {
            $path === 'companies' => 'companies.view',
            $path === 'dashboard' => 'dashboard.view',
            in_array($path, ['products', 'categories'], true) => 'products.view',
            $path === 'customers' => $request->isMethod('POST') ? 'customers.create' : 'customers.view',
            $path === 'payment-methods' => 'pos.checkout',
            $path === 'pos/reconciliation' => 'pos.reconciliation',
            preg_match('#^pos/sales/[^/]+/void$#', $path) === 1 => 'pos.sales.void',
            preg_match('#^pos/sales/[^/]+/cancel-payment$#', $path) === 1 => 'pos.checkout',
            str_starts_with($path, 'pos/sales') => 'pos.sales.view',
            str_starts_with($path, 'pos/shifts') => 'pos.shifts',
            str_starts_with($path, 'pos/held-bills') => 'pos.held-bills',
            str_starts_with($path, 'pos/') => 'pos.checkout',
            str_starts_with($path, 'billing/') => 'billing.view',
            default => null,
        };
        abort_unless($permission && $access->allows($user, $permission), 403, 'Anda tidak memiliki izin tindakan ini.');
        return $next($request);
    }
}

