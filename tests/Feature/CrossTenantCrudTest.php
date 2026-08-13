<?php

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

test('company admin cannot edit user from another tenant through direct id', function () {
    $companyA = Company::create(['name' => 'Tenant A', 'company_code' => 'CTA-'.uniqid(), 'is_active' => true, 'payment_due_at' => now()->addMonth()]);
    $companyB = Company::create(['name' => 'Tenant B', 'company_code' => 'CTB-'.uniqid(), 'is_active' => true, 'payment_due_at' => now()->addMonth()]);
    $roleA = Role::withoutGlobalScopes()->create(['role' => 'Admin A', 'company_id' => $companyA->id]);
    $roleB = Role::withoutGlobalScopes()->create(['role' => 'Admin B', 'company_id' => $companyB->id]);
    $admin = User::factory()->create(['company_id' => $companyA->id, 'role_id' => $roleA->id]);
    $otherUser = User::factory()->create(['company_id' => $companyB->id, 'role_id' => $roleB->id]);

    $this->actingAs($admin);

    expect(fn () => app(UserService::class)->edit(request(), $otherUser->id))->toThrow(HttpException::class);
});
