<?php

use App\Models\Company;
use App\Models\Pos\Product;
use App\Models\Pos\ProductCategory;
use App\Models\Pos\StockMovement;
use App\Models\Pos\Store;
use App\Models\Pos\Tax;
use App\Services\Pos\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

function makeStockProduct(Company $company, int $stock = 5): Product
{
    $tax = Tax::create(['company_id' => $company->id, 'name' => 'PPN', 'rate' => 10]);
    $category = ProductCategory::create(['company_id' => $company->id, 'name' => 'Minuman']);

    $product = new Product([
        'company_id' => $company->id,
        'name' => 'Teh',
        'price' => 50,
        'cost' => 25,
        'tax_id' => $tax->id,
        'category_id' => $category->id,
    ]);
    $product->sku = 'SKU-TEA-'.uniqid();
    $product->barcode = 'BC-TEA-'.uniqid();
    $product->track_stock = true;
    $product->stock_quantity = $stock;
    $product->save();

    return $product;
}

function stockRequest(string $uri, string $method, array $data): Request
{
    return Request::create($uri, $method, $data);
}

test('manual stock in increases product stock and records movement', function () {
    $company = Company::create(['name' => 'Test', 'company_code' => 'TEST-'.uniqid(), 'is_active' => true]);
    session(['company_id' => $company->id]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Toko', 'code' => 'ST-01']);
    $product = makeStockProduct($company, stock: 5);

    $service = new StockService();
    $response = $service->store(stockRequest('/admin-panel/stock/add', 'POST', [
        'store_id' => $store->id,
        'product_id' => $product->id,
        'quantity' => 3,
        'type' => 'in',
    ]));

    expect($response->getStatusCode())->toBe(302);
    expect($product->fresh()->stock_quantity)->toBe(8);

    $movement = StockMovement::where('product_id', $product->id)->first();
    expect($movement)->not->toBeNull()
        ->and($movement->quantity)->toBe('3')
        ->and($movement->type)->toBe('in')
        ->and($movement->product_variant_id)->toBeNull();
});

test('manual stock out decreases product stock', function () {
    $company = Company::create(['name' => 'Test', 'company_code' => 'TEST-'.uniqid(), 'is_active' => true]);
    session(['company_id' => $company->id]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Toko', 'code' => 'ST-01']);
    $product = makeStockProduct($company, stock: 5);

    $service = new StockService();
    $response = $service->store(stockRequest('/admin-panel/stock/add', 'POST', [
        'store_id' => $store->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'type' => 'out',
    ]));

    expect($response->getStatusCode())->toBe(302);
    expect($product->fresh()->stock_quantity)->toBe(3);

    $movement = StockMovement::where('product_id', $product->id)->first();
    expect($movement->quantity)->toBe('2')
        ->and($movement->type)->toBe('out');
});

test('manual stock out is rejected when stock is insufficient', function () {
    $company = Company::create(['name' => 'Test', 'company_code' => 'TEST-'.uniqid(), 'is_active' => true]);
    session(['company_id' => $company->id]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Toko', 'code' => 'ST-01']);
    $product = makeStockProduct($company, stock: 1);

    $service = new StockService();
    $response = $service->store(stockRequest('/admin-panel/stock/add', 'POST', [
        'store_id' => $store->id,
        'product_id' => $product->id,
        'quantity' => 5,
        'type' => 'out',
    ]));

    expect(session('failed'))->toContain('Insufficient Stock');
    expect($product->fresh()->stock_quantity)->toBe(1);
    expect(StockMovement::count())->toBe(0);
});

test('deleting a manual stock movement voids it and reverses its effect on product stock', function () {
    $company = Company::create(['name' => 'Test', 'company_code' => 'TEST-'.uniqid(), 'is_active' => true]);
    session(['company_id' => $company->id]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Toko', 'code' => 'ST-01']);
    $product = makeStockProduct($company, stock: 5);

    $service = new StockService();
    $service->store(stockRequest('/admin-panel/stock/add', 'POST', [
        'store_id' => $store->id,
        'product_id' => $product->id,
        'quantity' => 3,
        'type' => 'in',
    ]));
    expect($product->fresh()->stock_quantity)->toBe(8);

    $movement = StockMovement::where('product_id', $product->id)->first();
    $service->destroy($movement->id);

    expect($product->fresh()->stock_quantity)->toBe(5);
    expect(StockMovement::count())->toBe(1);
    expect($movement->fresh()->status)->toBe('void')
        ->and($movement->fresh()->voided_at)->not->toBeNull();
});

test('product without variant can be adjusted without product_variant_id', function () {
    $company = Company::create(['name' => 'Test', 'company_code' => 'TEST-'.uniqid(), 'is_active' => true]);
    session(['company_id' => $company->id]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Toko', 'code' => 'ST-01']);
    $product = makeStockProduct($company, stock: 5);

    $service = new StockService();
    $response = $service->store(stockRequest('/admin-panel/stock/add', 'POST', [
        'store_id' => $store->id,
        'product_id' => $product->id,
        'quantity' => 4,
        'type' => 'in',
    ]));

    expect($response->getStatusCode())->toBe(302);
    expect($product->fresh()->stock_quantity)->toBe(9);
});
