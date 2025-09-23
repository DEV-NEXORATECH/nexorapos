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
class AdminService
{
    public function adminPanel()
    {
        $userCount=User::count();
        $store=Store::count();
        $cats=ProductCategory::count();
        $products=Product::count();
        $variants=ProductVariant::count();
        $suppliers=Supplier::count();
        $customers=Customer::count();
        $taxes=Tax::count();
        $purchases=Purchase::count();
        $items=PurchaseItem::count();
        $sales=Sale::count();
        $payments=Sale::where('status','completed')->sum('total');
        return view('admin.dashboard', compact('userCount', 'store', 'cats', 'products', 'variants', 'suppliers', 'customers', 'taxes', 'purchases', 'items', 'sales', 'payments'));
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