<?php

use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('disabled company feature hides menu module and rejects direct route access', function () {
    $company = Company::create([
        'name' => 'Feature Gate',
        'company_code' => 'FG-'.uniqid(),
        'is_active' => true,
        'subscription_status' => 'active',
        'payment_due_at' => now()->addMonth()->toDateString(),
    ]);

    CompanyFeature::create(['company_id' => $company->id, 'feature_key' => 'pos', 'is_enabled' => true]);
    CompanyFeature::create(['company_id' => $company->id, 'feature_key' => 'suppliers', 'is_enabled' => false]);

    $category = PermissionCategory::create([
        'company_id' => $company->id,
        'name' => 'Suppliers',
        'feature_key' => 'suppliers',
        'display_order' => 1,
    ]);
    $permission = Permission::create([
        'company_id' => $company->id,
        'permission_category_id' => $category->id,
        'name' => 'Purchase Listing',
        'route' => 'admin-panel/purchase/listing',
        'is_show' => true,
    ]);
    $role = Role::withoutGlobalScopes()->create(['role' => 'Admin', 'company_id' => $company->id]);
    $role->permissions()->sync([$permission->id]);
    $user = User::factory()->create(['company_id' => $company->id, 'role_id' => $role->id]);

    $this->actingAs($user);

    expect(modules()->pluck('feature_key')->contains('suppliers'))->toBeFalse();

    $this->get('/admin-panel/purchase/listing')->assertForbidden();
});
