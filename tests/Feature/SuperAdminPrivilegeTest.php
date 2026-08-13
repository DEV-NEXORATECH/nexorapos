<?php

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

function sapCompany(string $code = 'SAP'): Company
{
    return Company::create([
        'name' => 'Privilege '.$code,
        'company_code' => $code.'-'.uniqid(),
        'is_active' => true,
        'subscription_status' => 'active',
        'payment_due_at' => now()->addMonth()->toDateString(),
    ]);
}

test('company admin cannot create super admin user', function () {
    $company = sapCompany();
    $role = Role::withoutGlobalScopes()->create(['role' => 'Admin', 'company_id' => $company->id]);
    $admin = User::factory()->create(['company_id' => $company->id, 'role_id' => $role->id, 'is_super_admin' => false]);

    $this->actingAs($admin);

    $request = Request::create('/admin-panel/users/add', 'POST', [
        'name' => 'Bad Actor',
        'email' => 'bad-'.uniqid().'@example.test',
        'phone' => '081234567891',
        'role_id' => $role->id,
        'password' => 'password',
        'company_id' => $company->id,
        'is_super_admin' => true,
    ]);

    expect(fn () => app(UserService::class)->store($request))->toThrow(HttpException::class);

    expect(User::where('email', $request->email)->exists())->toBeFalse();
});

test('company admin cannot move user to another company through update flow', function () {
    $companyA = sapCompany('SAPA');
    $companyB = sapCompany('SAPB');
    $roleA = Role::withoutGlobalScopes()->create(['role' => 'Admin A', 'company_id' => $companyA->id]);
    $roleB = Role::withoutGlobalScopes()->create(['role' => 'Admin B', 'company_id' => $companyB->id]);
    $admin = User::factory()->create(['company_id' => $companyA->id, 'role_id' => $roleA->id, 'is_super_admin' => false]);
    $target = User::factory()->create(['company_id' => $companyB->id, 'role_id' => $roleB->id, 'is_super_admin' => false]);

    $this->actingAs($admin);

    $request = Request::create('/admin-panel/users/update/'.$target->id, 'POST', [
        'name' => 'Moved User',
        'email' => $target->email,
        'phone' => $target->phone,
        'role_id' => $roleA->id,
        'company_id' => $companyA->id,
    ]);

    expect(fn () => app(UserService::class)->update($request, $target->id))->toThrow(HttpException::class);

    expect($target->fresh()->company_id)->toBe($companyB->id);
});
