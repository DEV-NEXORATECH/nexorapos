<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Feature;
use Illuminate\Http\Request;
use App\Services\Admin\AdminService;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function adminPanel()
    {
        return $this->adminService->adminPanel();
    }

    public function getSalesData(Request $request)
    {
        return $this->adminService->getSalesData($request);
    }

    public function businessProcess(Request $request)
    {
        $user = auth()->user();
        if ($user?->isSuperAdmin()) {
            return redirect('admin-panel/features')->with('failed', 'Business Process hanya untuk user company sebagai panduan penggunaan.');
        }

        $companies = $user?->isSuperAdmin()
            ? Company::orderBy('name')->get()
            : Company::where('id', $user?->company_id)->get();
        $selectedCompany = $user?->isSuperAdmin()
            ? $companies->firstWhere('id', (int) $request->get('company_id')) ?? $companies->first()
            : $user?->company;

        $features = Feature::orderBy('display_order')->orderBy('name')->get();
        $enabledFeatures = $features->filter(fn ($feature) => $selectedCompany?->hasFeature($feature->key));
        $disabledFeatures = $features->reject(fn ($feature) => $selectedCompany?->hasFeature($feature->key));
        $processSteps = $this->buildBusinessProcessSteps($enabledFeatures);

        return view('admin.business-process', compact('companies', 'selectedCompany', 'features', 'enabledFeatures', 'disabledFeatures', 'processSteps'));
    }

    private function buildBusinessProcessSteps($enabledFeatures): array
    {
        $hasAny = function (array $keywords) use ($enabledFeatures): bool {
            return $enabledFeatures->contains(function ($feature) use ($keywords) {
                $text = strtolower($feature->key.' '.$feature->name.' '.$feature->description);

                foreach ($keywords as $keyword) {
                    if (str_contains($text, $keyword)) {
                        return true;
                    }
                }

                return false;
            });
        };

        $steps = [
            [
                'label' => 'Login User',
                'title' => 'User masuk ke workspace',
                'description' => 'User login dengan email/phone dan password yang sudah dibuat admin.',
                'icon' => 'la la-sign-in',
                'enabled' => true,
            ],
            [
                'label' => 'License & RBAC',
                'title' => 'Cek lisensi, role, dan permission',
                'description' => 'Sistem memastikan company aktif, subscription aman, dan role user punya akses.',
                'icon' => 'la la-shield',
                'enabled' => true,
            ],
        ];

        $dynamicSteps = [
            [
                'keywords' => ['product', 'category', 'store', 'customer', 'supplier', 'tax', 'method'],
                'label' => 'Master Data',
                'title' => 'Admin menyiapkan data toko',
                'description' => 'Produk, kategori, pelanggan, toko, pajak, dan metode bayar disiapkan sesuai fitur aktif.',
                'icon' => 'la la-database',
            ],
            [
                'keywords' => ['stock', 'purchase', 'supplier', 'variant'],
                'label' => 'Inventory',
                'title' => 'Kelola stok bila paket mengaktifkan inventory',
                'description' => 'Pembelian, varian, supplier, dan pergerakan stok muncul hanya kalau fiturnya aktif.',
                'icon' => 'la la-cubes',
            ],
            [
                'keywords' => ['sale', 'pos'],
                'label' => 'POS',
                'title' => 'Kasir melakukan transaksi',
                'description' => 'Kasir pilih produk, atur quantity, checkout, lalu sistem membuat sale.',
                'icon' => 'la la-shopping-cart',
            ],
            [
                'keywords' => ['payment', 'method', 'invoice'],
                'label' => 'Payment & Receipt',
                'title' => 'Pembayaran dan struk customer',
                'description' => 'Pembayaran dicatat per metode, lalu struk bisa dicetak untuk customer.',
                'icon' => 'la la-credit-card',
            ],
            [
                'keywords' => ['report', 'reconciliation', 'sale', 'payment'],
                'label' => 'Reconciliation',
                'title' => 'Cocokkan pendapatan harian',
                'description' => 'Owner mengecek total struk, payment masuk, dan selisih per tanggal.',
                'icon' => 'la la-bar-chart',
            ],
            [
                'keywords' => ['setting', 'user', 'role', 'permission'],
                'label' => 'Admin Control',
                'title' => 'Atur user, role, dan konfigurasi',
                'description' => 'Admin mengatur akses user dan konfigurasi operasional sesuai kebutuhan company.',
                'icon' => 'la la-sliders',
            ],
        ];

        foreach ($dynamicSteps as $step) {
            if ($hasAny($step['keywords'])) {
                unset($step['keywords']);
                $step['enabled'] = true;
                $steps[] = $step;
            }
        }

        return $steps;
    }
}
