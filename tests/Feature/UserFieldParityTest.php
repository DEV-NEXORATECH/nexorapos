<?php

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('user edit supports phone and optional password parity with create', function () {
    $company = Company::create([
        'name' => 'Parity Company',
        'company_code' => 'PAR-'.uniqid(),
        'is_active' => true,
        'subscription_status' => 'active',
        'payment_due_at' => now()->addMonth()->toDateString(),
    ]);
    $role = Role::withoutGlobalScopes()->create(['role' => 'Admin', 'company_id' => $company->id]);
    $admin = User::factory()->create(['company_id' => $company->id, 'role_id' => $role->id]);
    $user = User::factory()->create(['company_id' => $company->id, 'role_id' => $role->id, 'phone' => '080000000001']);
    $oldPassword = $user->password;

    $this->actingAs($admin);

    $request = Request::create('/admin-panel/users/update/'.$user->id, 'POST', [
        'name' => 'Updated User',
        'email' => $user->email,
        'phone' => '089999999999',
        'role_id' => $role->id,
    ]);

    app(UserService::class)->update($request, $user->id);

    $user->refresh();
    expect($user->name)->toBe('Updated User')
        ->and($user->phone)->toBe('089999999999')
        ->and($user->password)->toBe($oldPassword);

    $passwordRequest = Request::create('/admin-panel/users/update/'.$user->id, 'POST', [
        'name' => 'Updated User',
        'email' => $user->email,
        'phone' => '089999999999',
        'role_id' => $role->id,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    app(UserService::class)->update($passwordRequest, $user->id);

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});
