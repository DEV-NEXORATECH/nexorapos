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

function makeMobileApiFixture(): array
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
        ['name' => 'Product List', 'route' => 'admin-panel/products/listing'],
        ['name' => 'Product Add', 'route' => 'admin-panel/products/add'],
        ['name' => 'Product Edit', 'route' => 'admin-panel/products/edit'],
    ])->map(fn ($permission) => Permission::create([
        'company_id' => $company->id,
        'permission_category_id' => $permissionCategory->id,
        'name' => $permission['name'],
        'route' => $permission['route'],
    ]));

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
        ->assertJsonPath('data.user.company.company_code', 'MOBILE');
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
    $fixture = makeMobileApiFixture();

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
    $fixture = makeMobileApiFixture();

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
