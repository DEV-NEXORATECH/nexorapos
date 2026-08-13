<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            // Ensure roles exist for the company
            $adminRole = Role::withoutGlobalScopes()->firstOrCreate([
                'role' => 'Admin',
                'company_id' => $company->id,
            ]);

            $kasirRole = Role::withoutGlobalScopes()->firstOrCreate([
                'role' => 'Kasir',
                'company_id' => $company->id,
            ]);

            $gudangRole = Role::withoutGlobalScopes()->firstOrCreate([
                'role' => 'Gudang',
                'company_id' => $company->id,
            ]);

            // Define Categories and their Permissions
            $featuresData = [
                'pos' => [
                    'name' => 'POS',
                    'permissions' => [
                        ['name' => 'Buka Kasir', 'route' => 'admin-panel/pos/sell'],
                    ],
                ],
                'sales' => [
                    'name' => 'Sales',
                    'permissions' => [
                        ['name' => 'Lihat Penjualan', 'route' => 'admin-panel/sales'],
                        ['name' => 'Daily Reconciliation', 'route' => 'admin-panel/sales/reconciliation'],
                    ],
                ],
                'products' => [
                    'name' => 'Products',
                    'permissions' => [
                        ['name' => 'Kelola Produk', 'route' => 'admin-panel/products'],
                        ['name' => 'Kelola Kategori', 'route' => 'admin-panel/products/categories'],
                        ['name' => 'Kelola Stok', 'route' => 'admin-panel/inventory'],
                    ],
                ],
                'customers' => [
                    'name' => 'Customers',
                    'permissions' => [
                        ['name' => 'Kelola Pelanggan', 'route' => 'admin-panel/customers'],
                    ],
                ],
                'suppliers' => [
                    'name' => 'Suppliers',
                    'permissions' => [
                        ['name' => 'Kelola Supplier', 'route' => 'admin-panel/suppliers'],
                        ['name' => 'Pembelian/Purchases', 'route' => 'admin-panel/purchases'],
                    ],
                ],
                'payments' => [
                    'name' => 'Payments',
                    'permissions' => [
                        ['name' => 'Kelola Metode Pembayaran', 'route' => 'admin-panel/payments'],
                    ],
                ],
                'reports' => [
                    'name' => 'Reports',
                    'permissions' => [
                        ['name' => 'Laporan Penjualan', 'route' => 'admin-panel/reports/sales'],
                        ['name' => 'Laporan Keuangan', 'route' => 'admin-panel/reports/financial'],
                    ],
                ],
            ];

            $adminPermissions = [];
            $kasirPermissions = [];
            $gudangPermissions = [];

            $order = 10;
            foreach ($featuresData as $featureKey => $data) {
                // Create Category
                $category = PermissionCategory::withoutGlobalScopes()->firstOrCreate([
                    'company_id' => $company->id,
                    'feature_key' => $featureKey,
                ], [
                    'name' => $data['name'],
                    'display_order' => $order,
                ]);

                foreach ($data['permissions'] as $permData) {
                    $permission = Permission::withoutGlobalScopes()->firstOrCreate([
                        'company_id' => $company->id,
                        'permission_category_id' => $category->id,
                        'name' => $permData['name'],
                    ], [
                        'route' => $permData['route'],
                    ]);

                    // Admin gets all
                    $adminPermissions[] = $permission->id;

                    // Mapping for Kasir
                    if (in_array($featureKey, ['pos', 'sales', 'customers', 'payments'])) {
                        // Exclude specific admin routes from kasir if necessary, but here we give kasir access to these basic features
                        if ($permData['name'] !== 'Daily Reconciliation') {
                            $kasirPermissions[] = $permission->id;
                        }
                    }

                    // Mapping for Gudang
                    if (in_array($featureKey, ['products', 'suppliers'])) {
                        $gudangPermissions[] = $permission->id;
                    }
                }
                $order += 10;
            }

            // Sync Permissions
            $adminRole->permissions()->sync($adminPermissions);
            $kasirRole->permissions()->sync($kasirPermissions);
            $gudangRole->permissions()->sync($gudangPermissions);
        }
    }
}
