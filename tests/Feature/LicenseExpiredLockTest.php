<?php

use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('expired license locks real admin panel business route with 423', function () {
    $company = Company::create([
        'name' => 'Expired Tenant',
        'company_code' => 'EXP-'.uniqid(),
        'is_active' => true,
        'subscription_status' => 'active',
        'payment_due_at' => now()->subDays(20)->toDateString(),
        'grace_period_days' => 7,
    ]);

    CompanyFeature::create(['company_id' => $company->id, 'feature_key' => 'products', 'is_enabled' => true]);
    $category = PermissionCategory::create(['company_id' => $company->id, 'name' => 'Products', 'feature_key' => 'products']);
    $permission = Permission::create([
        'company_id' => $company->id,
        'permission_category_id' => $category->id,
        'name' => 'Product Listing',
        'route' => 'admin-panel/products/listing',
        'is_show' => true,
    ]);
    $role = Role::withoutGlobalScopes()->create(['role' => 'Admin', 'company_id' => $company->id]);
    $role->permissions()->sync([$permission->id]);
    $user = User::factory()->create(['company_id' => $company->id, 'role_id' => $role->id]);

    $this->actingAs($user)
        ->get('/admin-panel/products/listing')
        ->assertStatus(423);
});
