<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['company_code' => 'NEXORA'],
            [
                'name' => 'Nexora Demo Company',
                'is_active' => true,
                'subscription_status' => 'active',
                'payment_due_at' => now()->addMonth()->toDateString(),
            ]
        );
        $company->forceFill([
            'name' => 'Nexora Demo Company',
            'is_active' => true,
            'subscription_status' => 'active',
            'billing_period' => $company->billing_period ?: 'monthly',
            'billing_interval_months' => $company->billing_interval_months ?: 1,
            'license_started_at' => $company->license_started_at ?: now()->toDateString(),
            'payment_due_at' => $company->payment_due_at ?: now()->addMonth()->toDateString(),
            'grace_period_days' => $company->grace_period_days ?? 7,
            'max_users' => $company->max_users ?: 25,
            'blocked_at' => null,
            'block_reason' => null,
        ])->save();

        $adminRole = Role::withoutGlobalScopes()->firstOrCreate([
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $kasirRole = Role::withoutGlobalScopes()->firstOrCreate([
            'role' => 'Kasir',
            'company_id' => $company->id,
        ]);

        $this->seedUser('superadmin@retailpos.test', [
            'name' => 'Super Admin',
            'phone' => '080000000000',
            'role_id' => null,
            'company_id' => null,
            'is_super_admin' => true,
        ]);

        $this->seedUser('admin@local.test', [
            'name' => 'Super Admin Local',
            'phone' => '080000000001',
            'role_id' => null,
            'company_id' => null,
            'is_super_admin' => true,
        ]);

        $this->seedUser('admin@nexora.test', [
            'name' => 'Nexora Admin',
            'phone' => '0881023806530',
            'role_id' => $adminRole->id,
            'company_id' => $company->id,
            'is_super_admin' => false,
        ]);

        $this->seedUser('test@example.com', [
            'name' => 'Test User',
            'phone' => '0881023806532',
            'role_id' => $adminRole->id,
            'company_id' => $company->id,
            'is_super_admin' => false,
        ]);

        $this->seedUser('kasir@nexora.test', [
            'name' => 'Kasir Demo',
            'phone' => '0881023806531',
            'role_id' => $kasirRole->id,
            'company_id' => $company->id,
            'is_super_admin' => false,
        ]);

        User::where('email', 'test@example.com')
            ->orWhere('email', 'admin@nexora.test')
            ->orWhere('email', 'kasir@nexora.test')
            ->update(['is_super_admin' => false]);

        $this->seedFeatures($company);
    }

    private function seedUser(string $email, array $attributes): void
    {
        User::withoutGlobalScopes()->updateOrCreate(
            ['email' => $email],
            $attributes + [
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedFeatures(Company $company): void
    {
        if (! Schema::hasTable('features') || ! Schema::hasTable('company_features')) {
            return;
        }

        $features = collect();

        if (Schema::hasTable('permission_categories')) {
            $features = DB::table('permission_categories')
                ->orderBy('display_order')
                ->orderBy('id')
                ->get()
                ->map(function ($category) {
                    $key = $category->feature_key ?: Str::slug($category->name, '_');

                    if (Schema::hasColumn('permission_categories', 'feature_key')) {
                        DB::table('permission_categories')
                            ->where('id', $category->id)
                            ->update(['feature_key' => $key]);
                    }

                    return [
                        'key' => $key,
                        'name' => $category->name,
                        'description' => 'Controls access to '.$category->name.' module.',
                        'display_order' => $category->display_order ?? 0,
                    ];
                });
        }

        if ($features->isEmpty()) {
            $features = collect([
                ['key' => 'pos', 'name' => 'POS Sell', 'description' => 'Checkout, invoice, and daily sales.', 'display_order' => 10],
                ['key' => 'products', 'name' => 'Products', 'description' => 'Products, categories, variants, and tax.', 'display_order' => 20],
                ['key' => 'customers', 'name' => 'Customers', 'description' => 'Customer database and profiles.', 'display_order' => 30],
                ['key' => 'suppliers', 'name' => 'Suppliers', 'description' => 'Supplier database and purchases.', 'display_order' => 40],
                ['key' => 'payments', 'name' => 'Payments', 'description' => 'Payment methods and payment records.', 'display_order' => 50],
                ['key' => 'reports', 'name' => 'Reports', 'description' => 'Sales reports and reconciliation.', 'display_order' => 60],
            ]);
        }

        $features->each(function (array $feature) {
            Feature::updateOrCreate(
                ['key' => $feature['key']],
                [
                    'name' => $feature['name'],
                    'description' => $feature['description'],
                    'display_order' => $feature['display_order'],
                    'is_active' => true,
                ]
            );
        });

        Company::query()->pluck('id')->each(function ($companyId) {
            Feature::query()->pluck('key')->each(function ($featureKey) use ($companyId) {
                CompanyFeature::updateOrCreate(
                    ['company_id' => $companyId, 'feature_key' => $featureKey],
                    ['is_enabled' => true]
                );
            });
        });
    }
}
