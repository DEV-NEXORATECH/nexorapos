<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permission_categories') || ! Schema::hasTable('permissions')) {
            return;
        }

        $this->ensureFeatures();

        $companies = Schema::hasTable('companies') ? DB::table('companies')->pluck('id') : collect([null]);

        $modules = [
            'pos' => [
                'category' => 'POS',
                'icon' => 'la la-cash-register',
                'display_order' => 10,
                'permissions' => [
                    'admin-panel/pos/sell' => 1,
                    'admin-panel/pos/cart/add' => 0,
                    'admin-panel/pos/cart/update' => 0,
                    'admin-panel/pos/cart/remove' => 0,
                    'admin-panel/pos/checkout' => 0,
                    'admin-panel/pos/invoice' => 0,
                    'admin-panel/pos/pdf' => 0,
                    'admin-panel/stores/listing' => 1,
                    'admin-panel/stores/add' => 0,
                    'admin-panel/stores/edit' => 0,
                    'admin-panel/stores/update' => 0,
                    'admin-panel/stores/delete' => 0,
                ],
            ],
            'sales' => [
                'category' => 'Sales',
                'icon' => 'la la-shopping-bag',
                'display_order' => 20,
                'permissions' => [
                    'admin-panel/sales/listing' => 1,
                    'admin-panel/sales/reconciliation' => 1,
                    'admin-panel/sales/add' => 0,
                    'admin-panel/sales/edit' => 0,
                    'admin-panel/sales/update' => 0,
                    'admin-panel/sales/delete' => 0,
                    'admin-panel/sale-items/listing' => 1,
                    'admin-panel/sale-items/add' => 0,
                    'admin-panel/sale-items/edit' => 0,
                    'admin-panel/sale-items/update' => 0,
                    'admin-panel/sale-items/delete' => 0,
                    'admin-panel/discounts/listing' => 1,
                    'admin-panel/discounts/add' => 0,
                    'admin-panel/discounts/edit' => 0,
                    'admin-panel/discounts/update' => 0,
                    'admin-panel/discounts/delete' => 0,
                ],
            ],
            'products' => [
                'category' => 'Products',
                'icon' => 'la la-cube',
                'display_order' => 30,
                'permissions' => [
                    'admin-panel/products/listing' => 1,
                    'admin-panel/products/add' => 0,
                    'admin-panel/products/edit' => 0,
                    'admin-panel/products/update' => 0,
                    'admin-panel/products/delete' => 0,
                    'admin-panel/products/export' => 0,
                    'admin-panel/products/import' => 0,
                    'admin-panel/products/recycle' => 0,
                    'admin-panel/products/restore' => 0,
                    'admin-panel/products/force-delete' => 0,
                    'admin-panel/categories/listing' => 1,
                    'admin-panel/categories/add' => 0,
                    'admin-panel/categories/edit' => 0,
                    'admin-panel/categories/update' => 0,
                    'admin-panel/categories/delete' => 0,
                    'admin-panel/variants/listing' => 1,
                    'admin-panel/variants/add' => 0,
                    'admin-panel/variants/edit' => 0,
                    'admin-panel/variants/update' => 0,
                    'admin-panel/variants/delete' => 0,
                    'admin-panel/tax/listing' => 1,
                    'admin-panel/tax/add' => 0,
                    'admin-panel/tax/edit' => 0,
                    'admin-panel/tax/update' => 0,
                    'admin-panel/tax/delete' => 0,
                    'admin-panel/stock/listing' => 1,
                    'admin-panel/stock/add' => 0,
                    'admin-panel/stock/edit' => 0,
                    'admin-panel/stock/update' => 0,
                    'admin-panel/stock/delete' => 0,
                ],
            ],
            'customers' => [
                'category' => 'Customers',
                'icon' => 'la la-address-book',
                'display_order' => 40,
                'permissions' => [
                    'admin-panel/customer/listing' => 1,
                    'admin-panel/customer/add' => 0,
                    'admin-panel/customer/edit' => 0,
                    'admin-panel/customer/update' => 0,
                    'admin-panel/customer/delete' => 0,
                ],
            ],
            'suppliers' => [
                'category' => 'Suppliers',
                'icon' => 'la la-truck',
                'display_order' => 50,
                'permissions' => [
                    'admin-panel/supplier/listing' => 1,
                    'admin-panel/supplier/add' => 0,
                    'admin-panel/supplier/edit' => 0,
                    'admin-panel/supplier/update' => 0,
                    'admin-panel/supplier/delete' => 0,
                    'admin-panel/purchase/listing' => 1,
                    'admin-panel/purchase/add' => 0,
                    'admin-panel/purchase/edit' => 0,
                    'admin-panel/purchase/update' => 0,
                    'admin-panel/purchase/delete' => 0,
                    'admin-panel/purchase-items/listing' => 1,
                    'admin-panel/purchase-item/add' => 0,
                    'admin-panel/purchase-items/edit' => 0,
                    'admin-panel/purchase-items/update' => 0,
                    'admin-panel/purchase-items/delete' => 0,
                ],
            ],
            'payments' => [
                'category' => 'Payments',
                'icon' => 'la la-money',
                'display_order' => 60,
                'permissions' => [
                    'admin-panel/payment-methods/listing' => 1,
                    'admin-panel/payment-methods/add' => 0,
                    'admin-panel/payment-methods/edit' => 0,
                    'admin-panel/payment-methods/update' => 0,
                    'admin-panel/payment-methods/delete' => 0,
                    'admin-panel/payments/listing' => 1,
                    'admin-panel/payments/add' => 0,
                    'admin-panel/payments/edit' => 0,
                    'admin-panel/payments/update' => 0,
                    'admin-panel/payments/delete' => 0,
                ],
            ],
        ];

        foreach ($companies as $companyId) {
            foreach ($modules as $featureKey => $module) {
                $categoryId = $this->ensureCategory($companyId, $featureKey, $module['category'], $module['icon'], $module['display_order']);

                foreach ($module['permissions'] as $route => $isShow) {
                    $permissionId = $this->ensurePermission($companyId, $categoryId, $route, $isShow);
                    $this->assignToAdminRoles($permissionId, $companyId);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permissions')) {
            DB::table('permissions')
                ->whereIn('route', [
                    'admin-panel/pos/sell',
                    'admin-panel/pos/cart/add',
                    'admin-panel/pos/cart/update',
                    'admin-panel/pos/cart/remove',
                    'admin-panel/pos/checkout',
                    'admin-panel/pos/invoice',
                    'admin-panel/pos/pdf',
                    'admin-panel/stores/listing',
                    'admin-panel/stores/add',
                    'admin-panel/stores/edit',
                    'admin-panel/stores/update',
                    'admin-panel/stores/delete',
                ])->delete();
        }
    }

    private function ensureFeatures(): void
    {
        if (! Schema::hasTable('features')) {
            return;
        }

        $features = [
            'pos' => ['POS Sell', 'POS screen, cart, and checkout.', 10],
            'sales' => ['Sales', 'Sales records, sale items, and discounts.', 20],
            'products' => ['Products', 'Products, categories, variants, tax, and stock.', 30],
            'customers' => ['Customers', 'Customer database and profiles.', 40],
            'suppliers' => ['Suppliers', 'Supplier database and purchases.', 50],
            'payments' => ['Payments', 'Payment methods and payment records.', 60],
        ];

        foreach ($features as $key => [$name, $description, $order]) {
            DB::table('features')->updateOrInsert(
                ['key' => $key],
                [
                    'name' => $name,
                    'description' => $description,
                    'display_order' => $order,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        if (Schema::hasTable('companies') && Schema::hasTable('company_features')) {
            DB::table('companies')->pluck('id')->each(function ($companyId) use ($features) {
                foreach (array_keys($features) as $featureKey) {
                    DB::table('company_features')->updateOrInsert(
                        ['company_id' => $companyId, 'feature_key' => $featureKey],
                        ['is_enabled' => true, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            });
        }
    }

    private function ensureCategory(?int $companyId, string $featureKey, string $name, string $icon, int $displayOrder): int
    {
        $query = DB::table('permission_categories')->where('name', $name);

        if (Schema::hasColumn('permission_categories', 'company_id')) {
            $query->where('company_id', $companyId);
        }

        $category = $query->orderBy('id')->first();

        if ($category) {
            if (Schema::hasColumn('permission_categories', 'feature_key') && ! $category->feature_key) {
                DB::table('permission_categories')
                    ->where('id', $category->id)
                    ->update(['feature_key' => $featureKey]);
            }

            return $category->id;
        }

        $payload = [
            'name' => $name,
            'icon' => $icon,
            'display_order' => $displayOrder,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('permission_categories', 'feature_key')) {
            $payload['feature_key'] = $featureKey;
        }

        if (Schema::hasColumn('permission_categories', 'company_id')) {
            $payload['company_id'] = $companyId;
        }

        return DB::table('permission_categories')->insertGetId($payload);
    }

    private function ensurePermission(?int $companyId, int $categoryId, string $route, int $isShow): int
    {
        $query = DB::table('permissions')
            ->where('permission_category_id', $categoryId)
            ->where('route', $route);

        if (Schema::hasColumn('permissions', 'company_id')) {
            $query->where('company_id', $companyId);
        }

        $existing = $query->first();

        if ($existing) {
            if (Schema::hasColumn('permissions', 'is_show') && (int) $existing->is_show !== $isShow) {
                DB::table('permissions')->where('id', $existing->id)->update([
                    'is_show' => $isShow,
                    'updated_at' => now(),
                ]);
            }

            return $existing->id;
        }

        $payload = [
            'permission_category_id' => $categoryId,
            'name' => ucwords(str_replace('-', ' ', basename(str_replace('admin-panel/', '', $route)))),
            'route' => $route,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('permissions', 'is_show')) {
            $payload['is_show'] = $isShow;
        }

        if (Schema::hasColumn('permissions', 'company_id')) {
            $payload['company_id'] = $companyId;
        }

        return DB::table('permissions')->insertGetId($payload);
    }

    private function assignToAdminRoles(int $permissionId, ?int $companyId): void
    {
        if (! Schema::hasTable('role_permissions') || ! Schema::hasTable('roles')) {
            return;
        }

        DB::table('roles')
            ->where('role', 'like', '%Admin%')
            ->when(Schema::hasColumn('roles', 'company_id'), fn ($query) => $query->where('company_id', $companyId))
            ->pluck('id')
            ->each(function ($roleId) use ($permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permissionId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            });
    }
};
