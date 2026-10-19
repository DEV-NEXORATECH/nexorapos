<?php

namespace App\Services;

use App\Models\User;

/** Shared authorization contract for the REST API and its mobile menu. */
class MobileAccess
{
    public const READ_ROUTES = [
        'pos.discount' => ['admin-panel/discounts/listing'],
        'pos.checkout' => ['admin-panel/pos/sell'],
        'pos.shifts' => ['admin-panel/pos/sell'],
        'pos.held-bills' => ['admin-panel/pos/sell'],
        'pos.sales.view' => ['admin-panel/sales/listing'],
        'pos.sales.void' => ['admin-panel/sales/void', 'admin-panel/sales/delete'],
        'pos.reconciliation' => ['admin-panel/sales/reconciliation'],
        'products.view' => ['admin-panel/products/listing', 'admin-panel/stock/listing', 'admin-panel/pos/sell'],
        'stock.view' => ['admin-panel/stock/listing'],
        'purchases.view' => ['admin-panel/purchase/listing'],
        'customers.view' => ['admin-panel/customer/listing', 'admin-panel/pos/sell'],
        'customers.create' => ['admin-panel/customer/add', 'admin-panel/customer/store'],
        'stores.view' => ['admin-panel/stores/listing'],
        'users.view' => ['admin-panel/users/listing'],
        'billing.view' => ['admin-panel/settings/company'],
    ];

    public function routes(User $user): array
    {
        if (! $user->role || (int) $user->role->company_id !== (int) $user->company_id) {
            return [];
        }
        return $user->role->permissions()->where('permissions.company_id', $user->company_id)->pluck('route')->all();
    }

    public function permissions(User $user): array
    {
        if ($user->isSuperAdmin()) {
            return ['*'];
        }
        $routes = $this->routes($user);
        $permissions = [];
        foreach (self::READ_ROUTES as $permission => $accepted) {
            if (array_intersect($accepted, $routes)) {
                $permissions[] = $permission;
            }
        }
        if ($routes) {
            $permissions[] = 'dashboard.view';
        }
        return $permissions;
    }

    public function allows(User $user, string $permission): bool
    {
        return $user->isSuperAdmin() || in_array($permission, $this->permissions($user), true);
    }

    public function resourceAllowed(User $user, string $resource, string $method): bool
    {
        if ($user->isSuperAdmin()) return true;
        $segments = [
            'users' => 'users', 'stores' => 'stores', 'categories' => 'categories', 'taxes' => 'tax',
            'products' => 'products', 'product-variants' => 'products', 'suppliers' => 'supplier',
            'customers' => 'customer', 'discounts' => 'discounts', 'payment-methods' => 'payment-methods',
            'purchases' => 'purchase', 'purchase-items' => 'purchase', 'stock-movements' => 'stock',
            'sales' => 'sales', 'sale-items' => 'sales', 'payments' => 'payment-methods',
        ];
        if (! isset($segments[$resource])) return false;
        $actions = match ($method) {
            'GET', 'HEAD' => ['listing'], 'POST' => ['add', 'store'],
            'PUT', 'PATCH' => ['edit', 'update'], 'DELETE' => ['delete', 'destroy', 'force-delete'],
            default => [],
        };
        foreach ($actions as $action) {
            if (in_array('admin-panel/'.$segments[$resource].'/'.$action, $this->routes($user), true)) return true;
        }
        return false;
    }
}
