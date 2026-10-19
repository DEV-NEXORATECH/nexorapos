<?php

use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\Pos\Method;
use App\Models\Pos\Product;
use App\Models\Pos\ProductCategory;
use App\Models\Pos\Sale;
use App\Models\Pos\SaleItem;
use App\Models\Pos\Store;
use App\Models\Pos\Tax;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function makeMobileApiFixture(bool $management = false): array
{
    $company = Company::create([
        'name' => 'Mobile Company',
        'company_code' => 'MOBILE',
        'is_active' => true,
        'subscription_status' => 'active',
        'license_started_at' => now()->toDateString(),
        'payment_due_at' => now()->addMonth()->toDateString(),
        'grace_period_days' => 7,
    ]);

    foreach (['pos', 'products', 'customers', 'suppliers', 'sales', 'payments'] as $index => $featureKey) {
        Feature::updateOrCreate(
            ['key' => $featureKey],
            ['name' => ucfirst($featureKey), 'description' => $featureKey.' mobile', 'display_order' => $index + 1, 'is_active' => true]
        );
        CompanyFeature::updateOrCreate(
            ['company_id' => $company->id, 'feature_key' => $featureKey],
            ['is_enabled' => true]
        );
    }

    $permissionCategory = PermissionCategory::create([
        'company_id' => $company->id,
        'name' => 'Products',
        'icon' => 'la la-box',
        'feature_key' => 'products',
        'display_order' => 1,
    ]);
    $permissions = collect([
        ['name' => 'POS Sell', 'route' => 'admin-panel/pos/sell'],
        ['name' => 'Sales List', 'route' => 'admin-panel/sales/listing'],
        ['name' => 'Sales Reconciliation', 'route' => 'admin-panel/sales/reconciliation'],
        ['name' => 'Sales Void', 'route' => 'admin-panel/sales/void'],
        ['name' => 'Product List', 'route' => 'admin-panel/products/listing'],
        ['name' => 'Product Add', 'route' => 'admin-panel/products/add'],
        ['name' => 'Product Edit', 'route' => 'admin-panel/products/edit'],
    ])->map(fn ($permission) => Permission::create([
        'company_id' => $company->id,
        'permission_category_id' => $permissionCategory->id,
        'name' => $permission['name'],
        'route' => $permission['route'],
    ]));

    if ($management) {
        foreach (['categories/add', 'products/add', 'stock/add', 'roles/add', 'users/add'] as $route) {
            $permissions->push(Permission::create(['company_id' => $company->id, 'permission_category_id' => $permissionCategory->id, 'name' => $route, 'route' => 'admin-panel/'.$route]));
        }
    }
    $role = Role::create(['company_id' => $company->id, 'role' => 'Kasir']);
    $role->permissions()->sync($permissions->pluck('id')->all());
    $user = User::factory()->create([
        'company_id' => $company->id,
        'role_id' => $role->id,
        'email' => 'mobile@example.test',
        'phone' => '088100000001',
        'password' => Hash::make('password'),
        'is_super_admin' => false,
    ]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Mobile Store', 'code' => 'MS-01']);
    $method = Method::create(['company_id' => $company->id, 'name' => 'Cash']);
    $tax = Tax::create(['company_id' => $company->id, 'name' => 'PPN', 'rate' => 10]);
    $category = ProductCategory::create(['company_id' => $company->id, 'name' => 'Drink']);
    $product = Product::create([
        'company_id' => $company->id,
        'tax_id' => $tax->id,
        'category_id' => $category->id,
        'name' => 'Tea',
        'sku' => 'TEA-'.uniqid(),
        'barcode' => 'BC-'.uniqid(),
        'price' => 100,
        'cost' => 50,
        'track_stock' => true,
        'stock_quantity' => 5,
        'minimum_stock' => 0,
    ]);

    return compact('company', 'user', 'store', 'method', 'product');
}

test('mobile api login returns bearer token and profile', function () {
    makeMobileApiFixture();

    $response = $this->postJson('/api/mobile/v1/auth/login', [
        'email' => 'mobile@example.test',
        'password' => 'password',
        'company_code' => 'MOBILE',
        'device_name' => 'Pest',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.user.company.company_code', 'MOBILE')->assertJsonPath('data.user.capabilities.checkout_idempotency', true)->assertJsonPath('data.user.capabilities.qris_recovery', true);
});

test('mobile api can refresh token and manage devices', function () {
    makeMobileApiFixture();

    $login = $this->postJson('/api/mobile/v1/auth/login', [
        'email' => 'mobile@example.test',
        'password' => 'password',
        'company_code' => 'MOBILE',
        'device_name' => 'Pest Device',
    ])->assertOk();

    $refresh = $this->postJson('/api/mobile/v1/auth/refresh', [
        'refresh_token' => $login->json('data.refresh_token'),
    ])->assertOk();

    $this->withToken($refresh->json('data.access_token'))
        ->getJson('/api/mobile/v1/auth/devices')
        ->assertOk()
        ->assertJsonPath('data.devices.0.name', 'Pest Device');
});

test('mobile api returns menu helper with action flags', function () {
    makeMobileApiFixture();

    $token = $this->postJson('/api/mobile/v1/auth/login', [
        'email' => 'mobile@example.test',
        'password' => 'password',
        'company_code' => 'MOBILE',
    ])->json('data.access_token');

    $this->withToken($token)
        ->getJson('/api/mobile/v1/admin/mobile-menu')
        ->assertOk()
        ->assertJsonPath('data.menus.0.name', 'Products')
        ->assertJsonPath('data.menus.0.can_view', true)
        ->assertJsonPath('data.menus.0.can_create', true)
        ->assertJsonPath('data.menus.0.can_update', true);
});

test('mobile api can read catalog and checkout sale stateless', function () {
    $fixture = makeMobileApiFixture();

    $token = $this->postJson('/api/mobile/v1/auth/login', [
        'email' => 'mobile@example.test',
        'password' => 'password',
        'company_code' => 'MOBILE',
    ])->json('data.access_token');

    $this->withToken($token)
        ->getJson('/api/mobile/v1/products')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data.products');

    $checkout = $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', [
        'store_id' => $fixture['store']->id,
        'items' => [
            [
                'product_id' => $fixture['product']->id,
                'quantity' => 1,
                'discount' => 0,
            ],
        ],
        'payments' => [
            [
                'method_id' => $fixture['method']->id,
                'amount' => 110,
            ],
        ],
    ]);

    $checkout->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.sale.total', '110.00');

    expect(Sale::count())->toBe(1)
        ->and(SaleItem::count())->toBe(1)
        ->and($fixture['product']->fresh()->stock_quantity)->toBe(4);
});

test('mobile checkout idempotency prevents duplicate sales', function () {
    $fixture = makeMobileApiFixture();

    $token = $this->postJson('/api/mobile/v1/auth/login', [
        'email' => 'mobile@example.test',
        'password' => 'password',
        'company_code' => 'MOBILE',
    ])->json('data.access_token');

    $payload = [
        'idempotency_key' => 'checkout-test-001',
        'store_id' => $fixture['store']->id,
        'items' => [
            ['product_id' => $fixture['product']->id, 'quantity' => 1],
        ],
        'payments' => [
            ['method_id' => $fixture['method']->id, 'amount' => 110],
        ],
    ];

    $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', $payload)->assertCreated();
    $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', $payload)
        ->assertOk()
        ->assertJsonPath('data.idempotent_replay', true);

    expect(Sale::count())->toBe(1)
        ->and($fixture['product']->fresh()->stock_quantity)->toBe(4);
});

test('mobile api can hold merge and split preview bills', function () {
    $fixture = makeMobileApiFixture();

    $token = $this->postJson('/api/mobile/v1/auth/login', [
        'email' => 'mobile@example.test',
        'password' => 'password',
        'company_code' => 'MOBILE',
    ])->json('data.access_token');

    $billOne = $this->withToken($token)->postJson('/api/mobile/v1/pos/held-bills', [
        'store_id' => $fixture['store']->id,
        'bill_name' => 'Table A',
        'table_name' => 'A1',
        'items' => [['product_id' => $fixture['product']->id, 'quantity' => 1]],
        'totals' => ['total' => 110],
    ])->assertCreated()->json('data.held_bill.id');

    $billTwo = $this->withToken($token)->postJson('/api/mobile/v1/pos/held-bills', [
        'store_id' => $fixture['store']->id,
        'bill_name' => 'Table B',
        'table_name' => 'B1',
        'items' => [['product_id' => $fixture['product']->id, 'quantity' => 2]],
        'totals' => ['total' => 220],
    ])->assertCreated()->json('data.held_bill.id');

    $this->withToken($token)->postJson('/api/mobile/v1/pos/held-bills/merge', [
        'held_bill_ids' => [$billOne, $billTwo],
        'bill_name' => 'Merged',
    ])->assertCreated()
        ->assertJsonPath('data.held_bill.totals_snapshot.total', 330);

    $this->withToken($token)->postJson('/api/mobile/v1/pos/split-preview', [
        'total' => 330,
        'splits' => [
            ['name' => 'A', 'amount' => 165],
            ['name' => 'B', 'amount' => 165],
        ],
    ])->assertOk()
        ->assertJsonPath('data.is_balanced', true);
});

test('mobile admin api can manage master data and stock movement', function () {
    $fixture = makeMobileApiFixture(true);

    $token = $this->postJson('/api/mobile/v1/auth/login', [
        'email' => 'mobile@example.test',
        'password' => 'password',
        'company_code' => 'MOBILE',
    ])->json('data.access_token');

    $categoryId = $this->withToken($token)->postJson('/api/mobile/v1/admin/categories', [
        'name' => 'Food',
    ])->assertCreated()->json('data.record.id');

    $productId = $this->withToken($token)->postJson('/api/mobile/v1/admin/products', [
        'name' => 'Rice',
        'sku' => 'RICE-'.uniqid(),
        'barcode' => 'RICE-BC-'.uniqid(),
        'category_id' => $categoryId,
        'tax_id' => Tax::where('company_id', $fixture['company']->id)->first()->id,
        'price' => 12000,
        'cost' => 8000,
        'track_stock' => true,
        'stock_quantity' => 3,
        'minimum_stock' => 1,
    ])->assertCreated()->json('data.record.id');

    $this->withToken($token)->postJson('/api/mobile/v1/admin/stock-movements', [
        'store_id' => $fixture['store']->id,
        'product_id' => $productId,
        'quantity' => 4,
        'type' => 'in',
    ])->assertCreated();

    expect(Product::find($productId)->stock_quantity)->toBe(7);

    $this->withToken($token)
        ->getJson('/api/mobile/v1/admin/products')
        ->assertOk()
        ->assertJsonPath('success', true);
});

test('mobile admin api can create role and user within company', function () {
    $fixture = makeMobileApiFixture(true);

    $token = $this->postJson('/api/mobile/v1/auth/login', [
        'email' => 'mobile@example.test',
        'password' => 'password',
        'company_code' => 'MOBILE',
    ])->json('data.access_token');

    $roleId = $this->withToken($token)->postJson('/api/mobile/v1/admin/roles', [
        'role' => 'Supervisor',
        'permission_ids' => [],
    ])->assertCreated()->json('data.role.id');

    $this->withToken($token)->postJson('/api/mobile/v1/admin/users', [
        'name' => 'Mobile Staff',
        'email' => 'staff-mobile@example.test',
        'phone' => '088100000002',
        'role_id' => $roleId,
        'store_id' => $fixture['store']->id,
        'shift_name' => 'Pagi',
        'shift_start' => '08:00',
        'shift_end' => '16:00',
        'password' => 'password',
    ])->assertCreated()->assertJsonPath('data.user.store.id', $fixture['store']->id);

    expect(User::where('email', 'staff-mobile@example.test')->where('company_id', $fixture['company']->id)->exists())->toBeTrue();
});


test('mobile session exposes server role id permissions and current shift', function () {
    $fixture = makeMobileApiFixture();
    $token = $this->postJson('/api/mobile/v1/auth/login', ['email' => 'mobile@example.test', 'password' => 'password', 'company_code' => 'MOBILE'])->json('data.access_token');
    $this->withToken($token)->getJson('/api/mobile/v1/auth/me')->assertOk()->assertJsonPath('data.user.role.id', $fixture['user']->role_id)->assertJsonPath('data.user.role.name', 'Kasir')->assertJsonPath('data.user.active_shift', null);
    $this->withToken($token)->postJson('/api/mobile/v1/pos/shifts/open', ['store_id' => $fixture['store']->id, 'opening_cash' => 0])->assertCreated();
    $this->withToken($token)->getJson('/api/mobile/v1/auth/me')->assertOk()->assertJsonPath('data.user.active_shift.status', 'open');
});

test('mobile denies direct actions when permission is absent and handles revocation immediately', function () {
    $fixture = makeMobileApiFixture();
    $token = $this->postJson('/api/mobile/v1/auth/login', ['email' => 'mobile@example.test', 'password' => 'password', 'company_code' => 'MOBILE'])->json('data.access_token');
    $this->withToken($token)->getJson('/api/mobile/v1/admin/users')->assertForbidden();
    $this->withToken($token)->postJson('/api/mobile/v1/admin/categories', ['name' => 'Forbidden'])->assertForbidden();
    $fixture['user']->role->permissions()->detach();
    $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', [])->assertForbidden();
    $this->withToken($token)->getJson('/api/mobile/v1/products')->assertForbidden();
});

test('mobile rejects a foreign company and an unassigned store', function () {
    $fixture = makeMobileApiFixture();
    $fixture['user']->update(['store_id' => $fixture['store']->id]);
    $other = Store::create(['company_id' => $fixture['company']->id, 'name' => 'Other store', 'code' => 'OTHER']);
    $token = $this->postJson('/api/mobile/v1/auth/login', ['email' => 'mobile@example.test', 'password' => 'password', 'company_code' => 'MOBILE'])->json('data.access_token');
    $this->withToken($token)->getJson('/api/mobile/v1/products?company_id=99999')->assertForbidden();
    $this->withToken($token)->getJson('/api/mobile/v1/products?store_id='.$other->id)->assertForbidden();
    $this->withToken($token)->getJson('/api/mobile/v1/stores')->assertOk()->assertJsonCount(1, 'data.stores');
});

test('warehouse gets inventory but cannot checkout without pos permission', function () {
    $fixture = makeMobileApiFixture();
    $role = $fixture['user']->role; $role->update(['role' => 'Gudang']);
    $role->permissions()->sync(Permission::where('company_id', $fixture['company']->id)->where('route', 'admin-panel/products/listing')->pluck('id')->all());
    $token = $this->postJson('/api/mobile/v1/auth/login', ['email' => 'mobile@example.test', 'password' => 'password', 'company_code' => 'MOBILE'])->json('data.access_token');
    $this->withToken($token)->getJson('/api/mobile/v1/auth/me')->assertOk()->assertJsonPath('data.user.role.name', 'Gudang');
    $this->withToken($token)->getJson('/api/mobile/v1/admin/mobile-menu')->assertOk();
    $this->withToken($token)->getJson('/api/mobile/v1/products')->assertOk();
    $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', [])->assertForbidden();
});

test('global super admin can restore menu before company selection', function () {
    $fixture = makeMobileApiFixture();
    $fixture['user']->update(['is_super_admin' => true, 'company_id' => null, 'role_id' => null]);
    $token = $this->postJson('/api/mobile/v1/auth/login', ['email' => 'mobile@example.test', 'password' => 'password'])->assertOk()->json('data.access_token');
    $this->withToken($token)->getJson('/api/mobile/v1/auth/me')->assertOk()->assertJsonPath('data.user.is_super_admin', true)->assertJsonPath('data.user.permissions.0', '*');
    $this->withToken($token)->getJson('/api/mobile/v1/admin/mobile-menu')->assertOk()->assertJsonPath('data.permissions.0', '*');
    $this->withToken($token)->getJson('/api/mobile/v1/companies')->assertOk();
});


test('checkout-only permission can read own invoice but not another cashier invoice', function () {
    $fixture = makeMobileApiFixture();
    $fixture['user']->role->permissions()->sync(Permission::where('company_id', $fixture['company']->id)->where('route', 'admin-panel/pos/sell')->pluck('id')->all());
    $token = $this->postJson('/api/mobile/v1/auth/login', ['email' => 'mobile@example.test', 'password' => 'password', 'company_code' => 'MOBILE'])->json('data.access_token');
    $saleId = $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', ['store_id' => $fixture['store']->id, 'items' => [['product_id' => $fixture['product']->id, 'quantity' => 1, 'discount' => 0]], 'payments' => [['method_id' => $fixture['method']->id, 'amount' => 110]], 'idempotency_key' => 'invoice-owner-test'])->assertCreated()->json('data.sale.id');
    $this->withToken($token)->getJson('/api/mobile/v1/pos/sales/'.$saleId)->assertOk();
    $other = User::factory()->create(['company_id' => $fixture['company']->id]);
    $otherSale = Sale::findOrFail($saleId)->replicate(); $otherSale->user_id = $other->id; $otherSale->invoice_number .= '-OTHER'; $otherSale->idempotency_key = 'other-invoice-key'; $otherSale->save();
    $this->withToken($token)->getJson('/api/mobile/v1/pos/sales/'.$otherSale->id)->assertNotFound();
    $this->withToken($token)->getJson('/api/mobile/v1/pos/sales')->assertForbidden();
});


function mobileCashierToken(): array {
    $fixture = makeMobileApiFixture();
    $token = test()->postJson('/api/mobile/v1/auth/login', ['email' => 'mobile@example.test', 'password' => 'password', 'company_code' => 'MOBILE'])->json('data.access_token');
    return [$fixture, $token];
}
function mobileCheckoutPayload(array $fixture, array $payments, string $key): array {
    return ['store_id' => $fixture['store']->id, 'items' => [['product_id' => $fixture['product']->id, 'quantity' => 1, 'discount' => 0]], 'payments' => $payments, 'idempotency_key' => $key];
}
function mobilePaidNotification($payment, string $status = 'settlement', ?string $amount = null): array {
    $gross = $amount ?? number_format((float) $payment->amount, 2, '.', '');
    return ['order_id' => $payment->order_id, 'status_code' => '200', 'gross_amount' => $gross, 'transaction_status' => $status, 'signature_key' => hash('sha512', $payment->order_id.'200'.$gross.'test-server-key')];
}

test('qris checkout remains unpaid and repeated creation reuses one gateway order', function () {
    [$f, $token] = mobileCashierToken();
    config(['services.midtrans.server_key' => 'test-server-key']);
    \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response(['token' => 'test-snap-token'], 201)]);
    $method = Method::create(['company_id' => $f['company']->id, 'name' => 'QRIS']);
    $saleId = $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', mobileCheckoutPayload($f, [['method_id' => $method->id, 'amount' => 110]], 'qris-safe'))->assertCreated()->assertJsonPath('data.sale.payment_status', 'UNPAID')->assertJsonPath('data.sale.status', 'pending')->json('data.sale.id');
    $one = $this->withToken($token)->postJson('/api/mobile/v1/pos/payments/qris', ['sale_id' => $saleId, 'amount' => 110])->assertCreated();
    $this->withToken($token)->postJson('/api/mobile/v1/pos/payments/qris', ['sale_id' => $saleId, 'amount' => 110])->assertOk()->assertJsonPath('data.order_id', $one->json('data.order_id'));
    \Illuminate\Support\Facades\Http::assertSentCount(1);
    expect(\App\Models\CompanyPayment::where('sale_id', $saleId)->count())->toBe(1);
    $this->withToken($token)->getJson('/api/mobile/v1/pos/sales/'.$saleId)->assertOk()->assertJsonPath('data.sale.qris_payment.order_id', $one->json('data.order_id'));
    $payment = \App\Models\CompanyPayment::where('sale_id', $saleId)->firstOrFail();
    $this->postJson('/api/midtrans/notification', mobilePaidNotification($payment))->assertOk();
    $this->postJson('/api/midtrans/notification', mobilePaidNotification($payment))->assertOk();
    $this->postJson('/api/midtrans/notification', mobilePaidNotification($payment, 'pending'))->assertOk();
    expect($payment->fresh()->status)->toBe('paid');
    expect(Sale::find($saleId)->payment_status)->toBe('PAID');
});

test('uncertain qris creation never creates a second gateway order', function () {
    [$f, $token] = mobileCashierToken(); config(['services.midtrans.server_key' => 'test-server-key']);
    \Illuminate\Support\Facades\Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('timeout'));
    $method = Method::create(['company_id' => $f['company']->id, 'name' => 'QRIS']);
    $saleId = $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', mobileCheckoutPayload($f, [['method_id' => $method->id, 'amount' => 110]], 'qris-timeout'))->assertCreated()->json('data.sale.id');
    $this->withToken($token)->postJson('/api/mobile/v1/pos/payments/qris', ['sale_id' => $saleId, 'amount' => 110])->assertStatus(502);
    $this->withToken($token)->postJson('/api/mobile/v1/pos/payments/qris', ['sale_id' => $saleId, 'amount' => 110])->assertStatus(409);
    expect(\App\Models\CompanyPayment::where('sale_id', $saleId)->count())->toBe(1);
    expect(Sale::find($saleId)->payment_status)->not->toBe('PAID');
});

test('cashier shift cash excludes qris after a split payment settles', function () {
    [$f, $token] = mobileCashierToken(); config(['services.midtrans.server_key' => 'test-server-key']);
    \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response(['token' => 'test-snap-token'], 201)]);
    $method = Method::create(['company_id' => $f['company']->id, 'name' => 'QRIS']);
    $this->withToken($token)->postJson('/api/mobile/v1/pos/shifts/open', ['store_id' => $f['store']->id, 'opening_cash' => 1000])->assertCreated();
    $saleId = $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', mobileCheckoutPayload($f, [['method_id' => $f['method']->id, 'amount' => 50], ['method_id' => $method->id, 'amount' => 60]], 'split-qris'))->assertCreated()->assertJsonPath('data.sale.payment_status', 'PARTIAL')->json('data.sale.id');
    $this->withToken($token)->postJson('/api/mobile/v1/pos/shifts/close', ['closing_cash' => 1050])->assertStatus(422);
    $this->withToken($token)->postJson('/api/mobile/v1/pos/payments/qris', ['sale_id' => $saleId, 'amount' => 60])->assertCreated();
    $payment = \App\Models\CompanyPayment::where('sale_id', $saleId)->firstOrFail();
    $this->postJson('/api/midtrans/notification', mobilePaidNotification($payment, 'settlement', '61.00'))->assertStatus(422);
    $this->postJson('/api/midtrans/notification', mobilePaidNotification($payment))->assertOk();
    $this->withToken($token)->getJson('/api/mobile/v1/pos/shifts/report')->assertOk()->assertJsonPath('data.summary.expected_cash', 1050);
    $this->withToken($token)->postJson('/api/mobile/v1/pos/shifts/close', ['closing_cash' => 1050])->assertOk();
});

test('mobile rejects invalid tender totals and unauthorized discounts', function () {
    [$f, $token] = mobileCashierToken();
    $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', mobileCheckoutPayload($f, [['method_id' => $f['method']->id, 'amount' => 100]], 'short'))->assertStatus(422);
    $payload = mobileCheckoutPayload($f, [['method_id' => $f['method']->id, 'amount' => 110]], 'discount'); $payload['items'][0]['discount'] = 1;
    $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', $payload)->assertForbidden();
});

test('held bill restore response is replayable and carries customer store and snapshot', function () {
    [$f, $token] = mobileCashierToken();
    $id = $this->withToken($token)->postJson('/api/mobile/v1/pos/held-bills', ['store_id' => $f['store']->id, 'bill_name' => 'Counter', 'items' => [['product_id' => $f['product']->id, 'quantity' => 1, 'discount' => 0]], 'totals' => ['total' => 110]])->assertCreated()->json('data.held_bill.id');
    $this->withToken($token)->postJson('/api/mobile/v1/pos/held-bills/'.$id.'/restore')->assertOk()->assertJsonPath('data.held_bill.store_id', $f['store']->id)->assertJsonCount(1, 'data.items');
    $this->withToken($token)->postJson('/api/mobile/v1/pos/held-bills/'.$id.'/restore')->assertOk()->assertJsonCount(1, 'data.items');
});


test('mobile paginated catalog finds variants and checkout uses server variant price', function () {
    [$f, $token] = mobileCashierToken();
    $variant = \App\Models\Pos\ProductVariant::create(['company_id' => $f['company']->id, 'product_id' => $f['product']->id, 'name' => 'Large Cup', 'sku' => 'VAR-L', 'barcode' => 'VARIANT123', 'price' => 150, 'cost' => 75]);
    $this->withToken($token)->getJson('/api/mobile/v1/products?search=VARIANT123&per_page=1')->assertOk()->assertJsonCount(1, 'data.products.data')->assertJsonPath('data.products.data.0.variants.0.id', $variant->id);
    $permission = Permission::create(['company_id' => $f['company']->id, 'permission_category_id' => PermissionCategory::where('company_id', $f['company']->id)->first()->id, 'name' => 'Discounts', 'route' => 'admin-panel/discounts/listing']);
    $f['user']->role->permissions()->attach($permission->id);
    $payload = mobileCheckoutPayload($f, [['method_id' => $f['method']->id, 'amount' => 308]], 'variant-price');
    $payload['items'][0] = ['product_id' => $f['product']->id, 'product_variant_id' => $variant->id, 'quantity' => 2, 'discount' => 10];
    $sale = $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', $payload)->assertCreated()->json('data.sale');
    expect((float) $sale['total'])->toBe(308.0); expect((float) $sale['discount'])->toBe(20.0); expect((float) $f['product']->fresh()->stock_quantity)->toBe(3.0);
});


test('admin analytics aggregates real paid sales in WIB and excludes another company', function () {
    $this->travelTo(\Carbon\CarbonImmutable::parse('2026-10-19 12:00:00', 'UTC'));
    [$f, $token] = mobileCashierToken(); $f['user']->role->update(['role' => 'Admin']);
    $id = $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', mobileCheckoutPayload($f, [['method_id' => $f['method']->id, 'amount' => 120]], 'dash-today'))->assertCreated()->json('data.sale.id');
    Sale::whereKey($id)->update(['created_at' => '2026-10-18 18:00:00']);
    $prevId = $this->withToken($token)->postJson('/api/mobile/v1/pos/checkout', mobileCheckoutPayload($f, [['method_id' => $f['method']->id, 'amount' => 110]], 'dash-prev'))->assertCreated()->json('data.sale.id');
    Sale::whereKey($prevId)->update(['created_at' => '2026-10-17 18:00:00']);
    $other = Company::create(['name' => 'Foreign Company', 'company_code' => 'FOREIGN', 'is_active' => true]);
    $foreign = Sale::findOrFail($id)->replicate(); $foreign->company_id = $other->id; $foreign->idempotency_key = 'foreign-sale'; $foreign->invoice_number .= '-FOREIGN'; $foreign->total = 999999; $foreign->save();
    $response = $this->withToken($token)->getJson('/api/mobile/v1/dashboard?analytics=1&from=2026-10-19&to=2026-10-19')->assertOk();
    $response->assertJsonPath('data.analytics.kpis.revenue', 110)->assertJsonPath('data.analytics.kpis.transactions', 1)->assertJsonPath('data.analytics.kpis.average_order_value', 110)->assertJsonPath('data.analytics.kpis.sales_growth', 0)->assertJsonPath('data.analytics.sales_series.0.date', '2026-10-19')->assertJsonPath('data.analytics.sales_series.0.value', 110)->assertJsonPath('data.analytics.payments.0.value', 110)->assertJsonPath('data.analytics.top_products.0.quantity', 1);
    $this->withToken($token)->getJson('/api/mobile/v1/dashboard?analytics=1&company_id='.$other->id)->assertForbidden();
    $this->withToken($token)->getJson('/api/mobile/v1/dashboard?scope=platform')->assertForbidden();
    $this->withToken($token)->getJson('/api/mobile/v1/companies?company_record_id='.$other->id)->assertForbidden();
});

test('admin dashboard enforces assigned store and validates custom period', function () {
    [$f, $token] = mobileCashierToken(); $f['user']->role->update(['role' => 'Admin']);
    $other = Store::create(['company_id' => $f['company']->id, 'name' => 'Other', 'code' => 'DASH-OTHER']);
    $f['user']->update(['store_id' => $f['store']->id]);
    $this->withToken($token)->getJson('/api/mobile/v1/dashboard?analytics=1&store_id='.$other->id)->assertForbidden();
    $this->withToken($token)->getJson('/api/mobile/v1/dashboard?analytics=1&from=2026-01-01&to=2026-10-19')->assertStatus(422);
    $this->withToken($token)->getJson('/api/mobile/v1/dashboard?analytics=1&from=2026-10-20&to=2026-10-19')->assertStatus(422);
    $this->withToken($token)->getJson('/api/mobile/v1/dashboard?analytics=1')->assertOk()->assertJsonPath('data.analytics.period.store_id', $f['store']->id)->assertJsonPath('data.analytics.kpis.revenue', 0)->assertJsonPath('data.analytics.kpis.sales_growth', null);
});

test('cashier cannot request admin financial analytics even with sales view', function () {
    [$f, $token] = mobileCashierToken();
    $this->withToken($token)->getJson('/api/mobile/v1/dashboard?analytics=1')->assertForbidden();
    $this->withToken($token)->getJson('/api/mobile/v1/dashboard')->assertOk();
});

test('super admin dashboard is platform-wide and billing excludes POS receipts', function () {
    $this->travelTo(\Carbon\CarbonImmutable::parse('2026-10-19 12:00:00', 'UTC'));
    [$f, $token] = mobileCashierToken(); $f['user']->update(['is_super_admin' => true]);
    Company::create(['name' => 'Inactive', 'company_code' => 'INACTIVE-DASH', 'is_active' => false, 'subscription_status' => 'inactive']);
    \App\Models\CompanyPayment::create(['company_id' => $f['company']->id, 'order_id' => 'LICENSE-DASH', 'amount' => 50000, 'purpose' => 'license', 'status' => 'paid', 'paid_at' => now()]);
    \App\Models\CompanyPayment::create(['company_id' => $f['company']->id, 'order_id' => 'POS-DASH', 'amount' => 999999, 'purpose' => 'pos', 'status' => 'paid', 'paid_at' => now()]);
    \App\Models\AuditLog::create(['company_id' => $f['company']->id, 'actor_user_id' => $f['user']->id, 'action' => 'update', 'module' => 'company', 'target_id' => $f['company']->id, 'created_at' => now()]);
    $response = $this->withToken($token)->getJson('/api/mobile/v1/dashboard?scope=platform&from=2026-10-19&to=2026-10-19&company_id='.$f['company']->id)->assertOk();
    $response->assertJsonPath('data.platform.kpis.billing_paid', 50000)->assertJsonCount(min(5, Company::count()), 'data.platform.latest_companies')->assertJsonPath('data.platform.activity.0.module', 'company')->assertJsonMissingPath('data.platform.activity.0.old_value');
    expect($response->json('data.platform.kpis.companies'))->toBe(Company::count());
    $this->withToken($token)->getJson('/api/mobile/v1/companies?company_record_id='.$f['company']->id)->assertOk()->assertJsonPath('data.company.company_code', 'MOBILE')->assertJsonPath('data.license.status', 'ACTIVE');
    $this->withToken($token)->getJson('/api/mobile/v1/admin/audit-logs?scope=platform')->assertOk()->assertJsonCount(1, 'data.audit_logs.data');
});
