<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\Pos\Customer;
use App\Models\Pos\Method;
use App\Models\Pos\Product;
use App\Models\Pos\ProductCategory;
use App\Models\Pos\Store;
use App\Models\Pos\Supplier;
use App\Models\Pos\Tax;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SumberSariSeeder extends Seeder
{
    private const COMPANY_CODE = 'SUMBERSARI';
    private const PASSWORD = 'password';

    public function run(): void
    {
        $company = Company::withoutGlobalScopes()->updateOrCreate(
            ['company_code' => self::COMPANY_CODE],
            [
                'name' => 'Sumber Sari',
                'legal_name' => 'Toko Kelontong Sumber Sari',
                'email' => 'sumbersari@example.test',
                'phone' => '081234567890',
                'address' => 'Jl. Sumber Sari No. 10',
                'city' => 'Bandung',
                'postal_code' => '40123',
                'is_active' => true,
                'max_users' => 10,
                'subscription_status' => 'active',
                'billing_period' => 'monthly',
                'billing_interval_months' => 1,
                'payment_amount' => 150000,
                'last_paid_at' => now()->toDateString(),
                'license_started_at' => now()->toDateString(),
                'payment_due_at' => now()->addMonthNoOverflow()->toDateString(),
                'grace_period_days' => 7,
                'blocked_at' => null,
                'block_reason' => null,
            ]
        );

        $this->enableFeatures($company);

        $adminRole = Role::withoutGlobalScopes()->firstOrCreate([
            'company_id' => $company->id,
            'role' => 'Admin',
        ]);
        $kasirRole = Role::withoutGlobalScopes()->firstOrCreate([
            'company_id' => $company->id,
            'role' => 'Kasir',
        ]);

        $mainStore = Store::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'code' => 'SS-MAIN'],
            ['name' => 'Sumber Sari - Toko Pusat']
        );
        Store::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'code' => 'SS-BRANCH'],
            ['name' => 'Sumber Sari - Cabang 1']
        );

        $this->seedUser($company, $adminRole, $mainStore, 'admin@sumbersari.test', 'Admin Sumber Sari', 'Office', '08:00', '17:00');
        $this->seedUser($company, $kasirRole, $mainStore, 'kasir@sumbersari.test', 'Kasir Sumber Sari', 'Pagi', '07:00', '15:00');

        $taxes = [
            'none' => Tax::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'name' => 'Tanpa Pajak'],
                ['rate' => 0]
            ),
            'ppn' => Tax::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'name' => 'PPN 11%'],
                ['rate' => 11]
            ),
        ];

        $categories = [];
        foreach (['Sembako', 'Makanan Instan', 'Snack', 'Minuman', 'Perawatan Diri', 'Rumah Tangga', 'Bumbu Dapur'] as $name) {
            $categories[$name] = ProductCategory::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'name' => $name], []
            );
        }

        foreach (['Cash', 'QRIS', 'Debit', 'Transfer'] as $name) {
            Method::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'name' => $name], []
            );
        }

        $products = [
            ['Sembako', 'Beras Ramos 5 kg', 'SS-BRS-001', 78000, 68000, 35],
            ['Sembako', 'Gula Pasir 1 kg', 'SS-SGR-001', 17500, 14500, 60],
            ['Sembako', 'Minyak Goreng 1 L', 'SS-MNY-001', 18500, 16000, 55],
            ['Sembako', 'Tepung Terigu 1 kg', 'SS-TPG-001', 13000, 10500, 40],
            ['Sembako', 'Telur Ayam 1 kg', 'SS-TLR-001', 30000, 27000, 30],
            ['Sembako', 'Susu Kental Manis 370 g', 'SS-SKM-001', 12500, 10000, 35],
            ['Sembako', 'Mi Telur 200 g', 'SS-MTL-001', 6500, 5000, 40],
            ['Makanan Instan', 'Mi Instan Goreng', 'SS-MIE-001', 3500, 2500, 120],
            ['Makanan Instan', 'Mi Instan Kari Ayam', 'SS-MIE-002', 3500, 2500, 120],
            ['Makanan Instan', 'Sarden Kaleng 155 g', 'SS-SRD-001', 14500, 11500, 30],
            ['Makanan Instan', 'Kornet Sapi 198 g', 'SS-KRN-001', 25000, 21000, 20],
            ['Snack', 'Chitato Original 68 g', 'SS-CHK-001', 11000, 8500, 50],
            ['Snack', 'Taro Net Seaweed 65 g', 'SS-TRN-001', 9000, 7000, 55],
            ['Snack', 'Qtela Singkong Balado 60 g', 'SS-QTL-001', 9500, 7500, 45],
            ['Snack', 'SilverQueen Mini 33 g', 'SS-SLV-001', 10500, 8500, 40],
            ['Snack', 'Oreo Original 119 g', 'SS-ORE-001', 11000, 8500, 45],
            ['Snack', 'Biskuit Roma Kelapa 300 g', 'SS-BSK-001', 12000, 9500, 35],
            ['Snack', 'Permen Kopiko 1 bungkus', 'SS-PMN-001', 8000, 6000, 50],
            ['Minuman', 'Air Mineral 600 ml', 'SS-AIR-001', 3500, 2200, 150],
            ['Minuman', 'Air Mineral 1.5 L', 'SS-AIR-002', 6000, 4000, 80],
            ['Minuman', 'Teh Botol Sosro 450 ml', 'SS-TEH-001', 5000, 3500, 70],
            ['Minuman', 'Pocari Sweat 500 ml', 'SS-POC-001', 8500, 6500, 50],
            ['Minuman', 'Kopi Good Day Botol', 'SS-KOP-001', 7000, 5000, 55],
            ['Minuman', 'Susu UHT Cokelat 250 ml', 'SS-SUS-001', 7500, 5500, 45],
            ['Minuman', 'Energen Cokelat Sachet', 'SS-ENG-001', 2500, 1700, 100],
            ['Perawatan Diri', 'Sabun Mandi Lifebuoy', 'SS-SBN-001', 4500, 3200, 50],
            ['Perawatan Diri', 'Shampoo Sachet', 'SS-SMP-001', 2000, 1300, 100],
            ['Perawatan Diri', 'Pasta Gigi 120 g', 'SS-ODL-001', 12500, 9500, 30],
            ['Perawatan Diri', 'Sikat Gigi', 'SS-SKT-001', 7000, 5000, 35],
            ['Rumah Tangga', 'Deterjen Bubuk 800 g', 'SS-DTR-001', 18000, 14500, 30],
            ['Rumah Tangga', 'Sabun Cuci Piring 400 ml', 'SS-SCP-001', 11000, 8500, 35],
            ['Rumah Tangga', 'Tisu Wajah  tissue', 'SS-TSU-001', 10000, 7500, 30],
            ['Rumah Tangga', 'Kantong Sampah Sedang', 'SS-KTS-001', 8000, 5500, 40],
            ['Bumbu Dapur', 'Garam Halus 500 g', 'SS-GRM-001', 5000, 3500, 40],
            ['Bumbu Dapur', 'Kaldu Ayam Sachet', 'SS-KLD-001', 2500, 1600, 80],
            ['Bumbu Dapur', 'Kecap Manis 135 ml', 'SS-KCP-001', 7500, 5500, 35],
            ['Bumbu Dapur', 'Saus Sambal 135 ml', 'SS-SAU-001', 7000, 5000, 35],
        ];

        foreach ($products as [$category, $name, $sku, $price, $cost, $stock]) {
            Product::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'sku' => $sku],
                [
                    'name' => $name,
                    'barcode' => '899'.str_pad((string) abs(crc32($sku)), 10, '0', STR_PAD_LEFT),
                    'category_id' => $categories[$category]->id,
                    'tax_id' => $taxes['none']->id,
                    'price' => $price,
                    'cost' => $cost,
                    'track_stock' => true,
                    'stock_quantity' => $stock,
                    'minimum_stock' => max(5, (int) floor($stock * 0.15)),
                ]
            );
        }

        Customer::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'customer_code' => 'SS-CUST-UMUM'],
            ['name' => 'Pelanggan Umum', 'email' => 'customer@sumbersari.test', 'phone' => '085700000001', 'address' => 'Walk-in customer']
        );
        Customer::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'customer_code' => 'SS-CUST-WARGA'],
            ['name' => 'Pelanggan Warga Sekitar', 'email' => 'warga@sumbersari.test', 'phone' => '085700000002', 'address' => 'Lingkungan Sumber Sari']
        );

        Supplier::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'email' => 'supplier@sumbersari.test'],
            ['name' => 'Distributor Sembako Sumber Makmur', 'phone' => '082200000001', 'address' => 'Gudang Distributor Bandung']
        );
        Supplier::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'email' => 'snack@sumbersari.test'],
            ['name' => 'Agen Snack dan Minuman Jaya', 'phone' => '082200000002', 'address' => 'Agen Snack Bandung']
        );

        foreach ([
            'receipt_footer' => 'Terima kasih sudah berbelanja di Sumber Sari.',
            'invoice_prefix' => 'SS-INV',
            'currency' => 'IDR',
            'timezone' => 'Asia/Jakarta',
            'company_display_name' => 'Sumber Sari',
        ] as $key => $value) {
            Setting::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'key' => $key],
                ['value' => $value]
            );
        }

        // Assign permissions after the company and its roles exist.
        $this->call(RolePermissionSeeder::class);

        $this->command?->info('Sumber Sari berhasil dibuat.');
        $this->command?->info('Admin: admin@sumbersari.test / password');
        $this->command?->info('Kasir: kasir@sumbersari.test / password');
    }

    private function seedUser(Company $company, Role $role, Store $store, string $email, string $name, string $shift, string $start, string $end): void
    {
        User::withoutGlobalScopes()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'company_id' => $company->id,
                'role_id' => $role->id,
                'store_id' => $store->id,
                'is_super_admin' => false,
                'password' => Hash::make(self::PASSWORD),
                'phone' => $email === 'admin@sumbersari.test' ? '081234567891' : '081234567892',
                'shift_name' => $shift,
                'shift_start' => $start,
                'shift_end' => $end,
                'email_verified_at' => now(),
            ]
        );
    }

    private function enableFeatures(Company $company): void
    {
        if (! class_exists(Feature::class)) {
            return;
        }

        foreach (Feature::pluck('key') as $featureKey) {
            CompanyFeature::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id, 'feature_key' => $featureKey],
                ['is_enabled' => true]
            );
        }
    }
}
