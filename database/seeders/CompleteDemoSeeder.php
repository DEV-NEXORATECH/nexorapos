<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\MobileApiToken;
use App\Models\Pos\CashierShift;
use App\Models\Pos\Customer;
use App\Models\Pos\Discount;
use App\Models\Pos\HeldBill;
use App\Models\Pos\Method;
use App\Models\Pos\Payment;
use App\Models\Pos\Product;
use App\Models\Pos\ProductCategory;
use App\Models\Pos\ProductVariant;
use App\Models\Pos\Purchase;
use App\Models\Pos\PurchaseItem;
use App\Models\Pos\Sale;
use App\Models\Pos\SaleItem;
use App\Models\Pos\StockMovement;
use App\Models\Pos\Store;
use App\Models\Pos\Supplier;
use App\Models\Pos\Tax;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CompleteDemoSeeder extends Seeder
{
    private const PASSWORD = 'password';

    public function run(): void
    {
        $this->seedFeatureMaster();

        foreach ($this->companies() as $companyData) {
            $company = $this->seedCompany($companyData);
            $this->seedCompanyFeatures($company, $companyData['disabled_features'] ?? []);
            $roles = $this->seedRoles($company);
            $stores = $this->seedStores($company, $companyData['store_prefix']);
            $users = $this->seedUsers($company, $roles, $stores, $companyData['user_prefix']);
            $taxes = $this->seedTaxes($company);
            $categories = $this->seedCategories($company);
            $methods = $this->seedPaymentMethods($company);
            $customers = $this->seedCustomers($company, $companyData['user_prefix']);
            $suppliers = $this->seedSuppliers($company, $companyData['user_prefix']);
            $products = $this->seedProducts($company, $categories, $taxes, $companyData['product_prefix']);
            $this->seedVariants($company, $products);
            $this->seedDiscounts($company);
            $this->seedSettings($company);
            $this->seedPurchases($company, $stores, $suppliers, $products);
            $shift = $this->seedShift($company, $stores[0], $users['kasir']);
            $this->seedSales($company, $stores[0], $users['kasir'], $customers[0], $methods, $products, $shift);
            $this->seedHeldBills($company, $stores[0], $users['kasir'], $customers[1], $products);
            $this->seedAuditLogs($company, $users['admin']);
            $this->seedMobileTokens($users);
        }
    }

    private function companies(): array
    {
        return [
            [
                'code' => 'NEXORA',
                'name' => 'Nexora Demo Company',
                'legal_name' => 'PT Nexora Demo Retail',
                'npwp' => '01.234.567.8-901.000',
                'email' => 'billing@nexora.test',
                'phone' => '0881023806530',
                'address' => 'Jl. Teknologi No. 10',
                'city' => 'Bandung',
                'postal_code' => '40111',
                'max_users' => 25,
                'payment_amount' => 250000,
                'billing_period' => 'monthly',
                'billing_interval_months' => 1,
                'store_prefix' => 'NX',
                'user_prefix' => 'nexora',
                'product_prefix' => 'NX',
            ],
            [
                'code' => 'RETAIL001',
                'name' => 'Retail Point Demo Mart',
                'legal_name' => 'CV Retail Point Demo Mart',
                'npwp' => '02.345.678.9-012.000',
                'email' => 'admin@retail001.test',
                'phone' => '081111111001',
                'address' => 'Jl. Niaga No. 21',
                'city' => 'Jakarta',
                'postal_code' => '10220',
                'max_users' => 10,
                'payment_amount' => 1000000,
                'billing_period' => 'yearly',
                'billing_interval_months' => 12,
                'store_prefix' => 'RT',
                'user_prefix' => 'retail001',
                'product_prefix' => 'RT',
            ],
            [
                'code' => 'CAFE001',
                'name' => 'Cafe Demo Simple POS',
                'legal_name' => 'Kedai Demo Sederhana',
                'npwp' => '03.456.789.0-123.000',
                'email' => 'owner@cafe001.test',
                'phone' => '081111111002',
                'address' => 'Jl. Kopi No. 7',
                'city' => 'Yogyakarta',
                'postal_code' => '55281',
                'max_users' => 5,
                'payment_amount' => 150000,
                'billing_period' => 'monthly',
                'billing_interval_months' => 1,
                'store_prefix' => 'CF',
                'user_prefix' => 'cafe001',
                'product_prefix' => 'CF',
                'disabled_features' => ['suppliers'],
            ],
        ];
    }

    private function seedFeatureMaster(): void
    {
        $features = [
            ['key' => 'pos', 'name' => 'POS', 'description' => 'Checkout, cart, invoice, held bill, and shift.', 'display_order' => 10],
            ['key' => 'sales', 'name' => 'Sales', 'description' => 'Sales list, invoice, reconciliation, void.', 'display_order' => 20],
            ['key' => 'products', 'name' => 'Products', 'description' => 'Products, categories, variants, tax, and stock.', 'display_order' => 30],
            ['key' => 'customers', 'name' => 'Customers', 'description' => 'Customer profile and quick POS customer.', 'display_order' => 40],
            ['key' => 'suppliers', 'name' => 'Suppliers', 'description' => 'Supplier and purchase flow.', 'display_order' => 50],
            ['key' => 'payments', 'name' => 'Payments', 'description' => 'Payment method and payment record.', 'display_order' => 60],
            ['key' => 'reports', 'name' => 'Reports', 'description' => 'Sales, shift, and reconciliation export.', 'display_order' => 70],
            ['key' => 'stores', 'name' => 'Stores', 'description' => 'Store or branch management.', 'display_order' => 80],
            ['key' => 'roles', 'name' => 'Roles & Access', 'description' => 'RBAC role and permission management.', 'display_order' => 90],
            ['key' => 'settings', 'name' => 'Settings', 'description' => 'Company profile and app setting.', 'display_order' => 100],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(['key' => $feature['key']], $feature + ['is_active' => true]);
        }
    }

    private function seedCompany(array $data): Company
    {
        return Company::updateOrCreate(
            ['company_code' => $data['code']],
            [
                'name' => $data['name'],
                'legal_name' => $data['legal_name'],
                'npwp' => $data['npwp'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'is_active' => true,
                'max_users' => $data['max_users'],
                'subscription_status' => 'active',
                'billing_period' => $data['billing_period'],
                'billing_interval_months' => $data['billing_interval_months'],
                'payment_amount' => $data['payment_amount'],
                'last_paid_at' => now()->subDays(2)->toDateString(),
                'license_started_at' => now()->startOfMonth()->toDateString(),
                'payment_due_at' => now()->addMonthsNoOverflow($data['billing_interval_months'])->toDateString(),
                'grace_period_days' => 7,
                'blocked_at' => null,
                'block_reason' => null,
            ]
        );
    }

    private function seedCompanyFeatures(Company $company, array $disabledFeatures): void
    {
        Feature::pluck('key')->each(function (string $featureKey) use ($company, $disabledFeatures) {
            CompanyFeature::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'feature_key' => $featureKey],
                ['is_enabled' => ! in_array($featureKey, $disabledFeatures, true)]
            );
        });
    }

    private function seedRoles(Company $company): array
    {
        return [
            'admin' => Role::withoutGlobalScopes()->firstOrCreate(['company_id' => $company->id, 'role' => 'Admin']),
            'kasir' => Role::withoutGlobalScopes()->firstOrCreate(['company_id' => $company->id, 'role' => 'Kasir']),
            'gudang' => Role::withoutGlobalScopes()->firstOrCreate(['company_id' => $company->id, 'role' => 'Gudang']),
            'owner' => Role::withoutGlobalScopes()->firstOrCreate(['company_id' => $company->id, 'role' => 'Owner']),
        ];
    }

    private function seedStores(Company $company, string $prefix): array
    {
        return [
            Store::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'code' => "{$prefix}-MAIN"],
                ['name' => "{$company->name} - Toko Pusat"]
            ),
            Store::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'code' => "{$prefix}-BRANCH"],
                ['name' => "{$company->name} - Cabang 1"]
            ),
        ];
    }

    private function seedUsers(Company $company, array $roles, array $stores, string $prefix): array
    {
        $users = [
            'admin' => [
                'name' => "{$company->name} Admin",
                'email' => "admin@{$prefix}.test",
                'phone' => '08120000'.str_pad((string) $company->id, 4, '0', STR_PAD_LEFT),
                'role_id' => $roles['admin']->id,
                'store_id' => $stores[0]->id,
                'shift_name' => 'Office',
                'shift_start' => '08:00',
                'shift_end' => '17:00',
            ],
            'kasir' => [
                'name' => "{$company->name} Kasir",
                'email' => "kasir@{$prefix}.test",
                'phone' => '08130000'.str_pad((string) $company->id, 4, '0', STR_PAD_LEFT),
                'role_id' => $roles['kasir']->id,
                'store_id' => $stores[0]->id,
                'shift_name' => 'Pagi',
                'shift_start' => '07:00',
                'shift_end' => '15:00',
            ],
            'gudang' => [
                'name' => "{$company->name} Gudang",
                'email' => "gudang@{$prefix}.test",
                'phone' => '08140000'.str_pad((string) $company->id, 4, '0', STR_PAD_LEFT),
                'role_id' => $roles['gudang']->id,
                'store_id' => $stores[1]->id,
                'shift_name' => 'Gudang',
                'shift_start' => '09:00',
                'shift_end' => '18:00',
            ],
        ];

        foreach ($users as $key => $user) {
            $users[$key] = User::withoutGlobalScopes()->updateOrCreate(
                ['email' => $user['email']],
                $user + [
                    'company_id' => $company->id,
                    'is_super_admin' => false,
                    'password' => Hash::make(self::PASSWORD),
                    'email_verified_at' => now(),
                ]
            );
        }

        return $users;
    }

    private function seedTaxes(Company $company): array
    {
        return [
            'none' => Tax::withoutGlobalScopes()->updateOrCreate(['company_id' => $company->id, 'name' => 'No Tax'], ['rate' => 0]),
            'ppn' => Tax::withoutGlobalScopes()->updateOrCreate(['company_id' => $company->id, 'name' => 'PPN 11%'], ['rate' => 11]),
        ];
    }

    private function seedCategories(Company $company): array
    {
        return collect(['Makanan', 'Minuman', 'Snack', 'Sembako', 'Layanan'])
            ->mapWithKeys(fn (string $name) => [
                $name => ProductCategory::withoutGlobalScopes()->updateOrCreate(['company_id' => $company->id, 'name' => $name]),
            ])
            ->all();
    }

    private function seedPaymentMethods(Company $company): array
    {
        return collect(['Cash', 'QRIS', 'Debit', 'Transfer'])
            ->mapWithKeys(fn (string $name) => [
                strtolower($name) => Method::withoutGlobalScopes()->updateOrCreate(['company_id' => $company->id, 'name' => $name]),
            ])
            ->all();
    }

    private function seedCustomers(Company $company, string $prefix): array
    {
        return [
            Customer::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'customer_code' => strtoupper($prefix).'-CUST-UMUM'],
                ['name' => 'Pelanggan Umum', 'email' => "customer@{$prefix}.test", 'phone' => '08510000'.str_pad((string) $company->id, 4, '0', STR_PAD_LEFT), 'address' => 'Walk-in customer']
            ),
            Customer::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'customer_code' => strtoupper($prefix).'-CUST-VIP'],
                ['name' => 'Pelanggan VIP', 'email' => "vip@{$prefix}.test", 'phone' => '08520000'.str_pad((string) $company->id, 4, '0', STR_PAD_LEFT), 'address' => 'Customer prioritas']
            ),
        ];
    }

    private function seedSuppliers(Company $company, string $prefix): array
    {
        return [
            Supplier::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'email' => "supplier@{$prefix}.test"],
                ['name' => "{$company->name} Supplier Utama", 'phone' => '08210000'.str_pad((string) $company->id, 4, '0', STR_PAD_LEFT), 'address' => 'Gudang supplier utama']
            ),
            Supplier::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'email' => "vendor@{$prefix}.test"],
                ['name' => "{$company->name} Vendor Cadangan", 'phone' => '08220000'.str_pad((string) $company->id, 4, '0', STR_PAD_LEFT), 'address' => 'Vendor cadangan']
            ),
        ];
    }

    private function seedProducts(Company $company, array $categories, array $taxes, string $prefix): array
    {
        $rows = [
            ['sku' => "{$prefix}-NASI-001", 'name' => 'Nasi Paket Ayam', 'category' => 'Makanan', 'price' => 22000, 'cost' => 15000, 'stock' => 80, 'tax' => 'ppn'],
            ['sku' => "{$prefix}-TEH-001", 'name' => 'Es Teh Manis', 'category' => 'Minuman', 'price' => 6000, 'cost' => 2500, 'stock' => 150, 'tax' => 'none'],
            ['sku' => "{$prefix}-KOPI-001", 'name' => 'Kopi Susu', 'category' => 'Minuman', 'price' => 18000, 'cost' => 9000, 'stock' => 90, 'tax' => 'ppn'],
            ['sku' => "{$prefix}-SNACK-001", 'name' => 'Keripik Original', 'category' => 'Snack', 'price' => 12000, 'cost' => 7000, 'stock' => 60, 'tax' => 'none'],
            ['sku' => "{$prefix}-BERAS-001", 'name' => 'Beras Premium 5kg', 'category' => 'Sembako', 'price' => 74000, 'cost' => 65000, 'stock' => 35, 'tax' => 'ppn'],
            ['sku' => "{$prefix}-CUSTOM", 'name' => 'Custom Amount', 'category' => 'Layanan', 'price' => 0, 'cost' => 0, 'stock' => 0, 'tax' => 'none', 'track_stock' => false],
        ];

        $products = [];
        foreach ($rows as $index => $row) {
            $products[$row['sku']] = Product::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'sku' => $row['sku']],
                [
                    'name' => $row['name'],
                    'barcode' => $row['sku'].'-BC',
                    'category_id' => $categories[$row['category']]->id,
                    'tax_id' => $taxes[$row['tax']]->id,
                    'price' => $row['price'],
                    'cost' => $row['cost'],
                    'track_stock' => $row['track_stock'] ?? true,
                    'stock_quantity' => $row['stock'],
                    'minimum_stock' => $index === 5 ? 0 : 10,
                ]
            );
        }

        return $products;
    }

    private function seedVariants(Company $company, array $products): void
    {
        foreach ($products as $product) {
            if (! $product->track_stock) {
                continue;
            }

            ProductVariant::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'product_id' => $product->id, 'sku' => $product->sku.'-REG'],
                ['name' => 'Regular', 'barcode' => $product->barcode.'-REG', 'price' => $product->price, 'cost' => $product->cost]
            );
            ProductVariant::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'product_id' => $product->id, 'sku' => $product->sku.'-L'],
                ['name' => 'Large', 'barcode' => $product->barcode.'-L', 'price' => ((float) $product->price) + 3000, 'cost' => ((float) $product->cost) + 1500]
            );
        }
    }

    private function seedDiscounts(Company $company): void
    {
        Discount::withoutGlobalScopes()->updateOrCreate(['company_id' => $company->id, 'name' => 'DISKON10'], ['type' => 'percentage', 'value' => 10]);
        Discount::withoutGlobalScopes()->updateOrCreate(['company_id' => $company->id, 'name' => 'POTONGAN5K'], ['type' => 'fixed', 'value' => 5000]);
    }

    private function seedSettings(Company $company): void
    {
        $settings = [
            'receipt_footer' => 'Terima kasih sudah berbelanja.',
            'invoice_prefix' => 'INV',
            'currency' => 'IDR',
            'timezone' => 'Asia/Jakarta',
            'company_display_name' => $company->name,
        ];

        foreach ($settings as $key => $value) {
            Setting::withoutGlobalScopes()->updateOrCreate(['company_id' => $company->id, 'key' => $key], ['value' => $value]);
        }
    }

    private function seedPurchases(Company $company, array $stores, array $suppliers, array $products): void
    {
        $purchase = Purchase::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'reference' => "{$company->company_code}-PO-0001"],
            ['supplier_id' => $suppliers[0]->id, 'total_amount' => 0, 'status' => 'received', 'cancelled_at' => null]
        );

        $total = 0;
        foreach (array_slice($products, 0, 3) as $product) {
            if (! $product->track_stock) {
                continue;
            }

            $quantity = 20;
            $total += $quantity * (float) $product->cost;
            PurchaseItem::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'purchase_id' => $purchase->id, 'product_id' => $product->id],
                ['quantity' => $quantity, 'cost' => $product->cost]
            );
            StockMovement::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'store_id' => $stores[0]->id, 'product_id' => $product->id, 'type' => 'purchase'],
                $this->stockMovementPayload($quantity, 'Seeder purchase stock')
            );
        }

        $purchase->update(['total_amount' => $total]);
    }

    private function seedShift(Company $company, Store $store, User $kasir): CashierShift
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return new CashierShift();
        }

        return CashierShift::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'store_id' => $store->id, 'user_id' => $kasir->id, 'shift_name' => 'Pagi Demo'],
            [
                'opening_cash' => 200000,
                'closing_cash' => 0,
                'expected_cash' => 200000,
                'cash_difference' => 0,
                'sales_count' => 0,
                'sales_total' => 0,
                'opened_at' => now()->setTime(7, 0),
                'closed_at' => null,
                'status' => 'open',
                'note' => 'Shift demo dari seeder',
            ]
        );
    }

    private function seedSales(Company $company, Store $store, User $kasir, Customer $customer, array $methods, array $products, ?CashierShift $shift): void
    {
        $items = [
            ['product' => reset($products), 'qty' => 2],
            ['product' => array_values($products)[1], 'qty' => 3],
        ];

        $subtotal = 0;
        $tax = 0;
        foreach ($items as $item) {
            $lineBase = (float) $item['product']->price * $item['qty'];
            $subtotal += $lineBase;
            $tax += round($lineBase * (float) ($item['product']->tax?->rate ?? 0) / 100, 2);
        }
        $discount = 5000;
        $total = $subtotal + $tax - $discount;

        $salePayload = [
            'store_id' => $store->id,
            'customer_id' => $customer->id,
            'user_id' => $kasir->id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'paid_amount' => $total,
            'change_amount' => 0,
            'status' => 'completed',
            'payment_status' => 'PAID',
        ];
        if (Schema::hasColumn('sales', 'cashier_shift_id') && $shift?->exists) {
            $salePayload['cashier_shift_id'] = $shift->id;
        }
        if (Schema::hasColumn('sales', 'idempotency_key')) {
            $salePayload['idempotency_key'] = "{$company->company_code}-SEED-CHECKOUT-0001";
        }

        $sale = Sale::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'invoice_number' => "{$company->company_code}-INV-0001"],
            $salePayload
        );

        foreach ($items as $item) {
            SaleItem::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'sale_id' => $sale->id, 'product_id' => $item['product']->id],
                [
                    'product_variant_id' => null,
                    'quantity' => $item['qty'],
                    'price' => $item['product']->price,
                    'discount' => 0,
                    'tax' => round(((float) $item['product']->price * $item['qty']) * (float) ($item['product']->tax?->rate ?? 0) / 100, 2),
                ]
            );
            StockMovement::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'store_id' => $store->id, 'product_id' => $item['product']->id, 'type' => 'sale'],
                $this->stockMovementPayload(-1 * $item['qty'], 'Seeder sale stock')
            );
        }

        Payment::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'sale_id' => $sale->id, 'reference' => "{$company->company_code}-PAY-0001"],
            ['method_id' => $methods['cash']->id, 'amount' => $total, 'status' => 'active']
        );

        if ($shift?->exists) {
            $shift->update([
                'sales_count' => 1,
                'sales_total' => $total,
                'expected_cash' => 200000 + $total,
            ]);
        }
    }

    private function seedHeldBills(Company $company, Store $store, User $kasir, Customer $customer, array $products): void
    {
        if (! Schema::hasTable('held_bills')) {
            return;
        }

        $product = array_values($products)[2];
        HeldBill::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'store_id' => $store->id, 'user_id' => $kasir->id, 'bill_name' => 'Meja A1 Demo'],
            [
                'customer_id' => $customer->id,
                'table_name' => 'A1',
                'cart_snapshot' => [
                    ['product_id' => $product->id, 'name' => $product->name, 'quantity' => 1, 'price' => $product->price],
                ],
                'totals_snapshot' => ['subtotal' => $product->price, 'tax' => 0, 'discount' => 0, 'total' => $product->price],
                'status' => 'held',
                'restored_at' => null,
            ]
        );
    }

    private function seedAuditLogs(Company $company, User $admin): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        AuditLog::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'actor_user_id' => $admin->id, 'action' => 'seed', 'module' => 'complete_demo'],
            [
                'target_type' => Company::class,
                'target_id' => $company->id,
                'old_value' => [],
                'new_value' => ['company_code' => $company->company_code, 'seeded' => true],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'CompleteDemoSeeder',
                'created_at' => now(),
            ]
        );
    }

    private function seedMobileTokens(array $users): void
    {
        if (! Schema::hasTable('mobile_api_tokens')) {
            return;
        }

        foreach (['admin', 'kasir'] as $key) {
            $plainToken = "demo-{$users[$key]->id}-access-token";
            $refreshToken = "demo-{$users[$key]->id}-refresh-token";
            $payload = [
                'name' => ucfirst($key).' Demo Device',
                'token_hash' => hash('sha256', $plainToken),
                'abilities' => ['*'],
                'last_used_at' => now(),
                'expires_at' => now()->addDays(30),
            ];
            if (Schema::hasColumn('mobile_api_tokens', 'refresh_token_hash')) {
                $payload['refresh_token_hash'] = hash('sha256', $refreshToken);
            }
            if (Schema::hasColumn('mobile_api_tokens', 'refresh_expires_at')) {
                $payload['refresh_expires_at'] = now()->addDays(60);
            }

            MobileApiToken::updateOrCreate(['user_id' => $users[$key]->id, 'name' => $payload['name']], $payload);
        }
    }

    private function stockMovementPayload(float|int $quantity, string $note): array
    {
        $payload = ['quantity' => $quantity];

        if (Schema::hasColumn('stock_movements', 'status')) {
            $payload['status'] = 'active';
        }

        if (Schema::hasColumn('stock_movements', 'note')) {
            $payload['note'] = $note;
        }

        return $payload;
    }
}
