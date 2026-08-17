<?php

use App\Models\Company;
use App\Models\Pos\CashierShift;
use App\Models\Pos\HeldBill;
use App\Models\Pos\Method;
use App\Models\Pos\Product;
use App\Models\Pos\ProductCategory;
use App\Models\Pos\Sale;
use App\Models\Pos\SaleItem;
use App\Models\Pos\StockMovement;
use App\Models\Pos\Store;
use App\Models\Pos\Tax;
use App\Models\User;
use App\Services\Pos\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

function makePosCompany(): Company
{
    return Company::create([
        'name' => 'Test Company',
        'company_code' => 'TEST-'.uniqid(),
        'is_active' => true,
    ]);
}

function makeProductWithStock(Company $company, int $stock = 5, float $price = 100, float $taxRate = 10): Product
{
    $tax = Tax::create(['company_id' => $company->id, 'name' => 'PPN', 'rate' => $taxRate]);
    $category = ProductCategory::create(['company_id' => $company->id, 'name' => 'Makanan']);

    $product = new Product([
        'company_id' => $company->id,
        'name' => 'Kopi',
        'price' => $price,
        'cost' => 50,
        'tax_id' => $tax->id,
        'category_id' => $category->id,
    ]);
    $product->sku = 'SKU-'.uniqid();
    $product->barcode = 'BC-'.uniqid();
    $product->track_stock = true;
    $product->stock_quantity = $stock;
    $product->save();

    return $product;
}

test('checkout computes totals server-side, applies tax rate, and decrements stock', function () {
    $company = makePosCompany();
    session(['company_id' => $company->id]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Toko Utama', 'code' => 'ST-01']);
    $method = Method::create(['company_id' => $company->id, 'name' => 'Cash']);
    $user = User::factory()->create(['company_id' => $company->id]);
    $product = makeProductWithStock($company, stock: 5, price: 100, taxRate: 10);

    $service = new SaleService();

    $add = Request::create('/admin-panel/pos/cart/add', 'POST', [
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $addResponse = $service->addToCart($add);
    expect($addResponse->getStatusCode())->toBe(200);

    $totals = json_decode($addResponse->getContent())->totals;
    expect((float) $totals->subtotal)->toBe(100.0)
        ->and((float) $totals->tax)->toBe(10.0)
        ->and((float) $totals->total)->toBe(110.0);

    $checkout = Request::create('/admin-panel/pos/checkout', 'POST', [
        'store_id' => $store->id,
        'method_id' => $method->id,
        'user_id' => $user->id,
    ]);
    $response = $service->checkout($checkout);
    expect($response->getStatusCode())->toBe(200);

    $payload = json_decode($response->getContent());
    expect($payload->success)->toBeTrue();

    $sale = Sale::find($payload->sale_id);
    expect((float) $sale->subtotal)->toBe(100.0)
        ->and((float) $sale->tax)->toBe(10.0)
        ->and((float) $sale->total)->toBe(110.0);

    expect($product->fresh()->stock_quantity)->toBe(4);

    expect(SaleItem::where('sale_id', $sale->id)->count())->toBe(1);
    $saleItem = SaleItem::where('sale_id', $sale->id)->first();
    expect((float) $saleItem->tax)->toBe(10.0);

    $movement = StockMovement::where('product_id', $product->id)->first();
    expect($movement)->not->toBeNull()
        ->and($movement->type)->toBe('sale')
        ->and($movement->quantity)->toBe('-1');

    expect(session('pos_cart'))->toBeNull();
});

test('checkout rejects sale when stock is insufficient', function () {
    $company = makePosCompany();
    session(['company_id' => $company->id]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Toko Utama', 'code' => 'ST-01']);
    $method = Method::create(['company_id' => $company->id, 'name' => 'Cash']);
    $user = User::factory()->create(['company_id' => $company->id]);
    $product = makeProductWithStock($company, stock: 1);

    $service = new SaleService();

    $add = Request::create('/admin-panel/pos/cart/add', 'POST', [
        'product_id' => $product->id,
        'quantity' => 5,
    ]);
    $service->addToCart($add);

    $checkout = Request::create('/admin-panel/pos/checkout', 'POST', [
        'store_id' => $store->id,
        'method_id' => $method->id,
        'user_id' => $user->id,
    ]);
    $response = $service->checkout($checkout);

    expect($response->getStatusCode())->toBe(422);
    expect(json_decode($response->getContent())->success)->toBeFalse();
    expect($product->fresh()->stock_quantity)->toBe(1);
    expect(Sale::count())->toBe(0);
});

test('custom amount becomes real sale item through custom amount product', function () {
    $company = makePosCompany();
    session(['company_id' => $company->id]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Toko Utama', 'code' => 'ST-01']);
    $method = Method::create(['company_id' => $company->id, 'name' => 'Cash']);
    $user = User::factory()->create(['company_id' => $company->id]);
    $service = new SaleService();

    $add = Request::create('/admin-panel/pos/cart/custom', 'POST', [
        'amount' => 25000,
        'name' => 'Extra charge',
    ]);
    $addResponse = $service->addCustomAmount($add);
    expect($addResponse->getStatusCode())->toBe(200);

    $checkout = Request::create('/admin-panel/pos/checkout', 'POST', [
        'store_id' => $store->id,
        'method_id' => $method->id,
        'user_id' => $user->id,
    ]);
    $response = $service->checkout($checkout);
    expect($response->getStatusCode())->toBe(200);

    $sale = Sale::find(json_decode($response->getContent())->sale_id);
    $item = SaleItem::where('sale_id', $sale->id)->first();
    $product = Product::find($item->product_id);

    expect((float) $sale->total)->toBe(25000.0)
        ->and((float) $item->price)->toBe(25000.0)
        ->and($product->sku)->toBe('CUSTOM-AMOUNT-'.$company->id)
        ->and((bool) $product->track_stock)->toBeFalse();
});

test('save bill stores cart server side and restore returns it to session', function () {
    $company = makePosCompany();
    session(['company_id' => $company->id]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Toko Utama', 'code' => 'ST-01']);
    $user = User::factory()->create(['company_id' => $company->id]);
    $product = makeProductWithStock($company);
    $service = new SaleService();

    $service->addToCart(Request::create('/admin-panel/pos/cart/add', 'POST', [
        'product_id' => $product->id,
        'quantity' => 1,
    ]));

    $holdResponse = $service->holdBill(Request::create('/admin-panel/pos/hold-bills', 'POST', [
        'store_id' => $store->id,
        'user_id' => $user->id,
        'bill_name' => 'Meja 1',
    ]));

    expect($holdResponse->getStatusCode())->toBe(200)
        ->and(session('pos_cart'))->toBeNull()
        ->and(HeldBill::where('bill_name', 'Meja 1')->where('status', 'held')->exists())->toBeTrue();

    $bill = HeldBill::where('bill_name', 'Meja 1')->first();
    $restoreResponse = $service->restoreHeldBill(
        Request::create('/admin-panel/pos/hold-bills/'.$bill->id.'/restore', 'POST', ['user_id' => $user->id]),
        $bill->id
    );

    expect($restoreResponse->getStatusCode())->toBe(200)
        ->and(session('pos_cart'))->not->toBeEmpty()
        ->and($bill->fresh()->status)->toBe('restored');
});

test('cashier shift opens, attaches sale, and closes with cash difference', function () {
    $company = makePosCompany();
    session(['company_id' => $company->id]);

    $store = Store::create(['company_id' => $company->id, 'name' => 'Toko Utama', 'code' => 'ST-01']);
    $method = Method::create(['company_id' => $company->id, 'name' => 'Cash']);
    $user = User::factory()->create(['company_id' => $company->id]);
    $product = makeProductWithStock($company, stock: 5, price: 100, taxRate: 0);
    $service = new SaleService();

    $open = $service->openShift(Request::create('/admin-panel/pos/shifts/open', 'POST', [
        'store_id' => $store->id,
        'user_id' => $user->id,
        'shift_name' => 'Pagi',
        'opening_cash' => 100000,
    ]));
    expect($open->getStatusCode())->toBe(200);

    $service->addToCart(Request::create('/admin-panel/pos/cart/add', 'POST', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]));

    $checkout = $service->checkout(Request::create('/admin-panel/pos/checkout', 'POST', [
        'store_id' => $store->id,
        'method_id' => $method->id,
        'user_id' => $user->id,
    ]));
    expect($checkout->getStatusCode())->toBe(200);

    $sale = Sale::find(json_decode($checkout->getContent())->sale_id);
    expect($sale->cashier_shift_id)->not->toBeNull();

    $close = $service->closeShift(Request::create('/admin-panel/pos/shifts/close', 'POST', [
        'user_id' => $user->id,
        'closing_cash' => 100250,
    ]));
    expect($close->getStatusCode())->toBe(200);

    $shift = CashierShift::first();
    expect($shift->status)->toBe('closed')
        ->and($shift->sales_count)->toBe(1)
        ->and((float) $shift->sales_total)->toBe(200.0)
        ->and((float) $shift->expected_cash)->toBe(100200.0)
        ->and((float) $shift->cash_difference)->toBe(50.0);
});
