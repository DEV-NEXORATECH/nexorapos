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
