<?php

use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

test('admin cannot grant permission from disabled company feature', function () {
    $company = Company::create([
        'name' => 'Role Cap',
        'company_code' => 'RC-'.uniqid(),
        'is_active' => true,
        'subscription_status' => 'active',
        'payment_due_at' => now()->addMonth()->toDateString(),
    ]);

    CompanyFeature::create(['company_id' => $company->id, 'feature_key' => 'products', 'is_enabled' => true]);
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

    $adminRole = Role::withoutGlobalScopes()->create(['role' => 'Admin', 'company_id' => $company->id]);
    $employeeRole = Role::withoutGlobalScopes()->create(['role' => 'Employee', 'company_id' => $company->id]);
    $adminRole->permissions()->sync([$permission->id]);
    $admin = User::factory()->create(['company_id' => $company->id, 'role_id' => $adminRole->id]);

    $this->actingAs($admin);

    $request = Request::create('/admin-panel/roles/update/'.$employeeRole->id, 'POST', [
        'role' => 'Employee',
        'permissions' => [$permission->id],
    ]);

    expect(fn () => app(RoleService::class)->update($request, $employeeRole->id))->toThrow(HttpException::class);

    expect($employeeRole->permissions()->count())->toBe(0);
});
