<?php

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

test('company admin cannot create user after max user limit is reached', function () {
    $company = Company::create([
        'name' => 'Limited Company',
        'company_code' => 'LIMIT-'.uniqid(),
        'is_active' => true,
        'subscription_status' => 'active',
        'payment_due_at' => now()->addMonth()->toDateString(),
        'max_users' => 1,
    ]);

    $role = Role::withoutGlobalScopes()->create(['role' => 'Admin', 'company_id' => $company->id]);
    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role_id' => $role->id,
        'is_super_admin' => false,
    ]);

    $this->actingAs($admin);

    $request = Request::create('/admin-panel/users/add', 'POST', [
        'name' => 'Employee One',
        'email' => 'employee-'.uniqid().'@example.test',
        'phone' => '081234567890',
        'role_id' => $role->id,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    app(UserService::class)->store($request);

    expect(User::where('company_id', $company->id)->count())->toBe(1);
});
