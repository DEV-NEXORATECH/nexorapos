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
                    'icon' => 'la la-cash-register',
                    'permissions' => [
                        ['name' => 'Buka Kasir', 'route' => 'admin-panel/pos/sell'],
                    ],
                ],
                'sales' => [
                    'name' => 'Sales',
                    'icon' => 'la la-shopping-bag',
                    'permissions' => [
                        ['name' => 'Lihat Penjualan', 'route' => 'admin-panel/sales/listing'],
                        ['name' => 'Daily Reconciliation', 'route' => 'admin-panel/sales/reconciliation'],
                    ],
                ],
                'products' => [
                    'name' => 'Products',
                    'icon' => 'la la-cube',
                    'permissions' => [
                        ['name' => 'Kelola Produk', 'route' => 'admin-panel/products/listing'],
                        ['name' => 'Kelola Kategori', 'route' => 'admin-panel/categories/listing'],
                        ['name' => 'Kelola Stok', 'route' => 'admin-panel/stock/listing'],
                    ],
                ],
                'customers' => [
                    'name' => 'Customers',
                    'icon' => 'la la-address-book',
                    'permissions' => [
                        ['name' => 'Kelola Pelanggan', 'route' => 'admin-panel/customer/listing'],
                    ],
                ],
                'suppliers' => [
                    'name' => 'Suppliers',
                    'icon' => 'la la-truck',
                    'permissions' => [
                        ['name' => 'Kelola Supplier', 'route' => 'admin-panel/supplier/listing'],
                        ['name' => 'Pembelian/Purchases', 'route' => 'admin-panel/purchase/listing'],
                    ],
                ],
                'payments' => [
                    'name' => 'Payments',
                    'icon' => 'la la-money',
                    'permissions' => [
                        ['name' => 'Kelola Metode Pembayaran', 'route' => 'admin-panel/payment-methods/listing'],
                    ],
                ],
                'reports' => [
                    'name' => 'Reports',
                    'icon' => 'la la-bar-chart',
                    'permissions' => [
                        ['name' => 'Rekonsiliasi Penjualan', 'route' => 'admin-panel/sales/reconciliation'],
                    ],
                ],
                'stores' => [
                    'name' => 'Stores',
                    'icon' => 'la la-home',
                    'permissions' => [
                        ['name' => 'Kelola Toko', 'route' => 'admin-panel/stores/listing'],
                    ],
                ],
                'taxes' => [
                    'name' => 'Taxes',
                    'icon' => 'la la-percent',
                    'permissions' => [
                        ['name' => 'Kelola Pajak', 'route' => 'admin-panel/tax/listing'],
                    ],
                ],
                'discounts' => [
                    'name' => 'Discounts',
                    'icon' => 'la la-ticket',
                    'permissions' => [
                        ['name' => 'Kelola Diskon', 'route' => 'admin-panel/discounts/listing'],
                    ],
                ],
                'roles' => [
                    'name' => 'Roles & Access',
                    'icon' => 'la la-key',
                    'permissions' => [
                        ['name' => 'Kelola Hak Akses', 'route' => 'admin-panel/roles/listing'],
                    ],
                ],
                'settings' => [
                    'name' => 'Settings',
                    'icon' => 'la la-cog',
                    'permissions' => [
                        ['name' => 'Pengaturan Sistem', 'route' => 'admin-panel/settings/listing'],
                        ['name' => 'Company Settings', 'route' => 'admin-panel/settings/company'],
                    ],
                ],
            ];

            $adminPermissions = [];
            $kasirPermissions = [];
            $gudangPermissions = [];

            $order = 10;
            foreach ($featuresData as $featureKey => $data) {
                // Create Category
                $category = PermissionCategory::withoutGlobalScopes()->updateOrCreate([
                    'company_id' => $company->id,
                    'feature_key' => $featureKey,
                ], [
                    'name' => $data['name'],
                    'icon' => $data['icon'],
                    'display_order' => $order,
                ]);

                foreach ($data['permissions'] as $permData) {
                    $permission = Permission::withoutGlobalScopes()->updateOrCreate([
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
