<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\Pos\Customer;
use App\Models\Pos\Method;
use App\Models\Pos\Payment;
use App\Models\Pos\Product;
use App\Models\Pos\ProductCategory;
use App\Models\Pos\Sale;
use App\Models\Pos\SaleItem;
use App\Models\Pos\Store;
use App\Models\Pos\Tax;
use App\Models\Pos\Supplier;
use App\Models\Pos\Discount;
use App\Models\Pos\ProductVariant;
use App\Models\Pos\Purchase;
use App\Models\Pos\PurchaseItem;
use App\Models\Pos\StockMovement;
use App\Models\Setting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $companiesData = [
            ['code' => 'COMPA', 'name' => 'Toko Serba Ada Company A'],
            ['code' => 'COMPB', 'name' => 'Toko Kelontong Company B'],
        ];

        foreach ($companiesData as $cData) {
            $company = Company::firstOrCreate(
                ['company_code' => $cData['code']],
                [
                    'name' => $cData['name'],
                    'is_active' => true,
                    'subscription_status' => 'active',
                    'payment_due_at' => now()->addMonth()->toDateString(),
                ]
            );

            // Give features
            Feature::pluck('key')->each(function ($featureKey) use ($company) {
                CompanyFeature::firstOrCreate(
                    ['company_id' => $company->id, 'feature_key' => $featureKey],
                    ['is_enabled' => true]
                );
            });

            // Role
            $roleAdmin = Role::withoutGlobalScopes()->firstOrCreate([
                'role' => 'Admin',
                'company_id' => $company->id,
            ]);
            
            $roleKasir = Role::withoutGlobalScopes()->firstOrCreate([
                'role' => 'Kasir',
                'company_id' => $company->id,
            ]);

            $roleGudang = Role::withoutGlobalScopes()->firstOrCreate([
                'role' => 'Gudang',
                'company_id' => $company->id,
            ]);

            // User
            $user = User::withoutGlobalScopes()->updateOrCreate(
                ['email' => strtolower($cData['code']) . '@example.com'],
                [
                    'name' => 'Admin ' . $cData['name'],
                    'phone' => '0812' . rand(10000000, 99999999),
                    'role_id' => $roleAdmin->id,
                    'company_id' => $company->id,
                    'is_super_admin' => false,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            
            $kasir = User::withoutGlobalScopes()->updateOrCreate(
                ['email' => 'kasir' . strtolower($cData['code']) . '@example.com'],
                [
                    'name' => 'Kasir ' . $cData['name'],
                    'phone' => '0813' . rand(10000000, 99999999),
                    'role_id' => $roleKasir->id,
                    'company_id' => $company->id,
                    'is_super_admin' => false,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $gudang = User::withoutGlobalScopes()->updateOrCreate(
                ['email' => 'gudang' . strtolower($cData['code']) . '@example.com'],
                [
                    'name' => 'Gudang ' . $cData['name'],
                    'phone' => '0814' . rand(10000000, 99999999),
                    'role_id' => $roleGudang->id,
                    'company_id' => $company->id,
                    'is_super_admin' => false,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Tax
            $tax = Tax::withoutGlobalScopes()->firstOrCreate([
                'company_id' => $company->id,
                'name' => 'PPN 11%',
            ], [
                'rate' => 11,
            ]);

            // Method
            $methodCash = Method::withoutGlobalScopes()->firstOrCreate([
                'company_id' => $company->id,
                'name' => 'Cash',
            ]);
            
            $methodQris = Method::withoutGlobalScopes()->firstOrCreate([
                'company_id' => $company->id,
                'name' => 'QRIS',
            ]);

            // Store
            $store = Store::withoutGlobalScopes()->firstOrCreate([
                'company_id' => $company->id,
                'name' => 'Toko Pusat ' . $cData['name'],
                'code' => 'ST-' . $cData['code'],
            ]);

            // Customer
            $customer = Customer::withoutGlobalScopes()->firstOrCreate([
                'company_id' => $company->id,
                'email' => 'customer' . $cData['code'] . '@example.com',
            ], [
                'name' => 'Pelanggan Umum ' . $cData['code'],
                'phone' => '0856' . rand(10000000, 99999999),
                'customer_code' => 'CUST-' . $cData['code'],
                'address' => 'Jl. Pelanggan No ' . rand(1, 50),
            ]);

            // Categories
            $categories = [];
            foreach (['Makanan', 'Minuman', 'Snack', 'Sembako'] as $catName) {
                $categories[] = ProductCategory::withoutGlobalScopes()->firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $catName,
                ]);
            }

            // Products
            $products = [];
            for ($i = 1; $i <= 10; $i++) {
                $cost = rand(5, 50) * 1000;
                $products[] = Product::withoutGlobalScopes()->firstOrCreate([
                    'company_id' => $company->id,
                    'sku' => 'PRD-' . $cData['code'] . '-' . $i,
                ], [
                    'name' => 'Produk ' . $i . ' ' . $cData['code'],
                    'barcode' => 'BC' . $cData['code'] . $i,
                    'category_id' => $categories[array_rand($categories)]->id,
                    'tax_id' => $tax->id,
                    'price' => $cost + rand(1, 10) * 1000,
                    'cost' => $cost,
                    'track_stock' => true,
                ]);
            }

            // Supplier
            $supplier = Supplier::withoutGlobalScopes()->firstOrCreate([
                'company_id' => $company->id,
                'email' => 'supplier' . $cData['code'] . '@example.com',
            ], [
                'name' => 'Supplier Utama ' . $cData['code'],
                'phone' => '0821' . rand(10000000, 99999999),
                'address' => 'Gudang Supplier No ' . rand(1, 10),
            ]);

            // Discount
            Discount::withoutGlobalScopes()->firstOrCreate([
                'company_id' => $company->id,
                'name' => 'DISKON10',
            ], [
                'type' => 'percentage',
                'value' => 10,
            ]);
            Discount::withoutGlobalScopes()->firstOrCreate([
                'company_id' => $company->id,
                'name' => 'POTONGAN5K',
            ], [
                'type' => 'fixed',
                'value' => 5000,
            ]);

            // Setting
            $settingsData = [
                'login_method' => 'email',
                'newsletter' => 'Dapatkan info promo pos menarik setiap minggu!',
                'about_us' => 'Nexorapos adalah solusi manajemen kasir pintar untuk bisnis Anda.',
                'facebook' => 'https://facebook.com/nexorapos',
                'instagram' => 'https://instagram.com/nexorapos',
                'youtube' => 'https://youtube.com/nexorapos',
                'twitter' => 'https://twitter.com/nexorapos',
            ];
            foreach ($settingsData as $key => $val) {
                Setting::withoutGlobalScopes()->updateOrCreate(
                    ['company_id' => $company->id, 'key' => $key],
                    ['value' => $val]
                );
            }

            // ProductVariant
            $variants = [];
            foreach ($products as $prod) {
                $variants[] = ProductVariant::withoutGlobalScopes()->firstOrCreate([
                    'company_id' => $company->id,
                    'product_id' => $prod->id,
                    'sku' => $prod->sku . '-V1',
                ], [
                    'name' => 'Ukuran L',
                    'barcode' => $prod->barcode . 'V1',
                    'price' => $prod->price + 2000,
                    'cost' => $prod->cost + 1000,
                ]);
                $variants[] = ProductVariant::withoutGlobalScopes()->firstOrCreate([
                    'company_id' => $company->id,
                    'product_id' => $prod->id,
                    'sku' => $prod->sku . '-V2',
                ], [
                    'name' => 'Ukuran M',
                    'barcode' => $prod->barcode . 'V2',
                    'price' => $prod->price,
                    'cost' => $prod->cost,
                ]);
            }

            // Purchase & PurchaseItem
            $purchase = Purchase::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'supplier_id' => $supplier->id,
                'reference' => 'PO-' . $cData['code'] . '-' . date('Ymd') . '-' . rand(100, 999),
                'total_amount' => 0,
                'status' => 'received',
            ]);
            $pSubtotal = 0;
            foreach (array_slice($products, 0, 3) as $prod) {
                $qty = rand(10, 50);
                $pSubtotal += $qty * $prod->cost;
                PurchaseItem::withoutGlobalScopes()->create([
                    'company_id' => $company->id,
                    'purchase_id' => $purchase->id,
                    'product_id' => $prod->id,
                    'quantity' => $qty,
                    'cost' => $prod->cost,
                ]);
                
                // Stock movement for purchase (in)
                StockMovement::withoutGlobalScopes()->create([
                    'company_id' => $company->id,
                    'store_id' => $store->id,
                    'product_id' => $prod->id,
                    'quantity' => $qty,
                    'type' => 'in',
                    'status' => 'completed',
                ]);
            }
            $purchase->update(['total_amount' => $pSubtotal]);

            // Sales
            for ($i = 1; $i <= 5; $i++) {
                $subtotal = 0;
                $sale = Sale::withoutGlobalScopes()->create([
                    'company_id' => $company->id,
                    'store_id' => $store->id,
                    'customer_id' => $customer->id,
                    'user_id' => $user->id,
                    'subtotal' => 0,
                    'tax' => 0,
                    'discount' => 0,
                    'total' => 0,
                    'paid_amount' => 0,
                    'change_amount' => 0,
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'invoice_number' => 'INV-' . $cData['code'] . '-' . date('YmdHis') . '-' . rand(1000, 9999),
                ]);

                $selectedProducts = array_rand($products, rand(1, 3));
                if (!is_array($selectedProducts)) {
                    $selectedProducts = [$selectedProducts];
                }
                
                foreach ($selectedProducts as $idx) {
                    $prod = $products[$idx];
                    $qty = rand(1, 5);
                    $price = $prod->price;
                    $totalItemPrice = $qty * $price;
                    $subtotal += $totalItemPrice;

                    SaleItem::withoutGlobalScopes()->create([
                        'company_id' => $company->id,
                        'sale_id' => $sale->id,
                        'product_id' => $prod->id,
                        'quantity' => $qty,
                        'price' => $price,
                        'discount' => 0,
                        'tax' => 0,
                    ]);
                }

                $taxAmt = $subtotal * ($tax->rate / 100);
                $total = $subtotal + $taxAmt;

                $sale->update([
                    'subtotal' => $subtotal,
                    'tax' => $taxAmt,
                    'total' => $total,
                    'paid_amount' => $total,
                ]);

                Payment::withoutGlobalScopes()->create([
                    'company_id' => $company->id,
                    'sale_id' => $sale->id,
                    'method_id' => ($i % 2 == 0) ? $methodQris->id : $methodCash->id,
                    'amount' => $total,
                ]);
            }
        }
    }
}
