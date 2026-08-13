<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\Pos\Method;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SuperAdminSeeder::class);

        // User::factory(10)->create();
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
            'subscription_status' => $company->subscription_status ?: 'active',
            'payment_due_at' => $company->payment_due_at ?: now()->addMonth()->toDateString(),
        ])->save();

        $role = Role::withoutGlobalScopes()->firstOrCreate([
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        foreach (['Cash', 'QRIS', 'Transfer'] as $methodName) {
            Method::withoutGlobalScopes()->firstOrCreate([
                'company_id' => $company->id,
                'name' => $methodName,
            ]);
        }

        Feature::pluck('key')->each(function ($featureKey) use ($company) {
            CompanyFeature::firstOrCreate(
                ['company_id' => $company->id, 'feature_key' => $featureKey],
                ['is_enabled' => true]
            );
        });

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'phone' => '0881023806532',
                'role_id' => $role->id,
                'company_id' => $company->id,
                'is_super_admin' => false,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call(DummyDataSeeder::class);
        $this->call(RolePermissionSeeder::class);
    }
}
