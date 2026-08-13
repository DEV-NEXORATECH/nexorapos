<?php

use App\Http\Middleware\CheckCompanyLicense;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

function licenseCompany(array $overrides = []): Company
{
    return Company::create(array_merge([
        'name' => 'License Company',
        'company_code' => 'LIC-'.uniqid(),
        'is_active' => true,
        'subscription_status' => 'active',
        'payment_due_at' => now()->addMonth()->toDateString(),
        'grace_period_days' => 7,
    ], $overrides));
}

function licenseUser(Company $company): User
{
    $role = Role::withoutGlobalScopes()->create(['role' => 'Admin', 'company_id' => $company->id]);

    return User::factory()->create([
        'company_id' => $company->id,
        'role_id' => $role->id,
        'is_super_admin' => false,
    ]);
}

test('expired company receives 423 on protected backend endpoint', function () {
    Route::middleware(['auth', CheckCompanyLicense::class])->get('/license-test/protected', fn () => response()->json(['ok' => true]));

    $company = licenseCompany([
        'payment_due_at' => now()->subDays(10)->toDateString(),
        'grace_period_days' => 7,
    ]);

    $this->actingAs(licenseUser($company))
        ->getJson('/license-test/protected')
        ->assertStatus(423)
        ->assertJsonPath('code', 'LICENSE_EXPIRED');
});

test('grace period company can still access protected backend endpoint', function () {
    Route::middleware(['auth', CheckCompanyLicense::class])->get('/license-test/grace', fn () => response()->json(['ok' => true]));

    $company = licenseCompany([
        'payment_due_at' => now()->subDays(2)->toDateString(),
        'grace_period_days' => 7,
    ]);

    $this->actingAs(licenseUser($company))
        ->getJson('/license-test/grace')
        ->assertOk()
        ->assertJsonPath('ok', true);
});

test('suspended company has priority over expiry state', function () {
    $company = licenseCompany([
        'is_active' => false,
        'subscription_status' => 'blocked',
        'payment_due_at' => now()->addMonth()->toDateString(),
    ]);

    expect($company->licenseStatus())->toBe('SUSPENDED');
});
