<?php

namespace App\Services\Admin;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Models\Pos\Store;
use App\Models\Pos\Product;
use App\Models\Pos\ProductCategory;
use App\Models\Pos\ProductVariant;
use App\Models\Pos\Supplier;
use App\Models\Pos\Customer;
use App\Models\Pos\Tax;
use App\Models\Pos\Purchase;
use App\Models\Pos\PurchaseItem;
use App\Models\Pos\Sale;
use App\Models\Pos\Payment;
use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
class AdminService
{
    public function adminPanel()
    {
        $user = auth()->user();
        $companyId = auth()->user()?->company_id;
        $userCount=User::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $store=Store::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $cats=ProductCategory::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $products=Product::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $variants=ProductVariant::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $suppliers=Supplier::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $customers=Customer::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $taxes=Tax::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $purchases=Purchase::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $items=PurchaseItem::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $sales=Sale::when($companyId, fn ($query) => $query->where('company_id', $companyId))->count();
        $payments=Payment::when($companyId, fn ($query) => $query->where('company_id', $companyId))->sum('amount');
        $platformStats = null;

        if ($user?->isSuperAdmin()) {
            $companies = Company::query()->get();
            $platformStats = [
                'total_companies' => $companies->count(),
                'active_companies' => $companies->where('is_active', true)->count(),
                'active_licenses' => $companies->filter(fn ($company) => ! $company->isLicenseInactive())->count(),
                'expiring_licenses' => $companies->filter(fn ($company) => $company->licenseSnapshot()['is_warning'] ?? false)->count(),
                'total_users' => User::count(),
                'enabled_features' => CompanyFeature::where('is_enabled', true)->count().' / '.(max($companies->count(), 1) * max(Feature::count(), 1)),
            ];
        }

        return view('admin.dashboard', compact('userCount', 'store', 'cats', 'products', 'variants', 'suppliers', 'customers', 'taxes', 'purchases', 'items', 'sales', 'payments', 'platformStats'));
    }

  
  public function getSalesData(Request $request)
{
    $filter = $request->get('filter', 'monthly'); // default monthly to match JS
    $salesData = [];

    if ($filter === 'daily') {
        // Group sales by HOUR
        $sales = Sale::selectRaw('HOUR(created_at) as hour, SUM(total) as total')
            ->groupBy('hour')->orderBy('hour')->get();

        for ($i = 0; $i < 24; $i++) {
            $salesData[] = [
                'label' => $i . ':00',
                'value' => (float) ($sales->firstWhere('hour', $i)->total ?? 0),
            ];
        }

    } elseif ($filter === 'weekly') {
        // Group sales by DAY OF WEEK (1 = Sunday … 7 = Saturday)
        $sales = Sale::selectRaw('DAYOFWEEK(created_at) as day, SUM(total) as total')
            ->groupBy('day')->orderBy('day')->get();

        $days = [1=>'Sun', 2=>'Mon', 3=>'Tue', 4=>'Wed', 5=>'Thu', 6=>'Fri', 7=>'Sat'];

        foreach ($days as $key => $dayName) {
            $salesData[] = [
                'label' => $dayName,
                'value' => (float) ($sales->firstWhere('day', $key)->total ?? 0),
            ];
        }

    } elseif ($filter === 'monthly') {
        // Group sales by MONTH
        $sales = Sale::selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('month')->orderBy('month')->get();

        $months = [1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'May', 6=>'Jun',
                   7=>'Jul', 8=>'Aug', 9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dec'];

        foreach ($months as $num => $name) {
            $salesData[] = [
                'label' => $name,
                'value' => (float) ($sales->firstWhere('month', $num)->total ?? 0),
            ];
        }
    }

    return response()->json($salesData);
}
    
}
