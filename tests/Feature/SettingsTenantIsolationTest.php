<?php

use App\Models\Company;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('settings list is isolated per tenant', function () {
    $companyA = Company::create(['name' => 'Settings A', 'company_code' => 'STA-'.uniqid(), 'is_active' => true]);
    $companyB = Company::create(['name' => 'Settings B', 'company_code' => 'STB-'.uniqid(), 'is_active' => true]);
    $roleA = Role::withoutGlobalScopes()->create(['role' => 'Admin A', 'company_id' => $companyA->id]);
    $userA = User::factory()->create(['company_id' => $companyA->id, 'role_id' => $roleA->id]);

    Setting::withoutGlobalScopes()->create(['company_id' => $companyA->id, 'key' => 'receipt_footer', 'value' => 'A']);
    Setting::withoutGlobalScopes()->create(['company_id' => $companyB->id, 'key' => 'receipt_footer', 'value' => 'B']);

    $this->actingAs($userA);

    expect(Setting::pluck('value')->all())->toBe(['A']);
});
