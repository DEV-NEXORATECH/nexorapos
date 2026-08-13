<?php

use App\Models\Company;
use App\Models\Pos\Store;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function auditCompany(string $code): Company
{
    return Company::create([
        'name' => 'Company '.$code,
        'company_code' => $code.'-'.uniqid(),
        'is_active' => true,
        'subscription_status' => 'active',
        'payment_due_at' => now()->addMonth()->toDateString(),
    ]);
}

function auditUserForCompany(Company $company, bool $superAdmin = false): User
{
    $role = Role::withoutGlobalScopes()->create([
        'role' => $superAdmin ? 'Super Admin' : 'Admin',
        'company_id' => $company->id,
    ]);

    return User::factory()->create([
        'company_id' => $company->id,
        'role_id' => $role->id,
        'is_super_admin' => $superAdmin,
    ]);
}

test('tenant-scoped models only return current company records', function () {
    $companyA = auditCompany('A');
    $companyB = auditCompany('B');
    $userA = auditUserForCompany($companyA);

    Store::create(['company_id' => $companyA->id, 'name' => 'Store A', 'code' => 'A1']);
    Store::create(['company_id' => $companyB->id, 'name' => 'Store B', 'code' => 'B1']);

    $this->actingAs($userA);

    expect(Store::count())->toBe(1)
        ->and(Store::first()->name)->toBe('Store A');
});

test('settings are tenant-scoped and cache keys do not leak across company', function () {
    $companyA = auditCompany('SA');
    $companyB = auditCompany('SB');
    $userA = auditUserForCompany($companyA);
    $userB = auditUserForCompany($companyB);

    Setting::withoutGlobalScopes()->create(['company_id' => $companyA->id, 'key' => 'invoice_prefix', 'value' => 'A']);
    Setting::withoutGlobalScopes()->create(['company_id' => $companyB->id, 'key' => 'invoice_prefix', 'value' => 'B']);

    $this->actingAs($userA);
    expect(Setting::cachedValue('invoice_prefix'))->toBe('A');

    $this->actingAs($userB);
    expect(Setting::cachedValue('invoice_prefix'))->toBe('B');
});
