<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\Pos\Customer;
use App\Models\Pos\Method;
use App\Models\Pos\Product;
use App\Models\Pos\ProductCategory;
use App\Models\Pos\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CatalogController extends Controller
{
    public function dashboard(Request $request)
    {
        if ($request->query('scope') === 'platform') {
            abort_unless($request->user()->isSuperAdmin(), 403);
            return $this->json(true, 'Dashboard platform.', ['platform' => app(\App\Services\MobileDashboard::class)->platform($request)]);
        }
        $companyId = $this->companyId($request);
        $analytics = $request->boolean('analytics') ? app(\App\Services\MobileDashboard::class)->company($request, (int) $companyId) : null;

        return $this->json(true, 'Dashboard mobile.', [
            'company' => $this->companyPayload($request, $companyId),
            'analytics' => $analytics,
            'counts' => [
                'stores' => Store::where('company_id', $companyId)->count(),
                'categories' => ProductCategory::where('company_id', $companyId)->count(),
                'products' => Product::where('company_id', $companyId)->count(),
                'customers' => Customer::where('company_id', $companyId)->count(),
                'payment_methods' => Method::where('company_id', $companyId)->count(),
            ],
        ]);
    }

    public function companies(Request $request)
    {
        if (! $request->user()?->isSuperAdmin()) {
            return $this->json(false, 'Hanya Super Admin yang bisa melihat seluruh company.', null, 403);
        }

        if ($request->query('company_record_id')) {
            $record = Company::findOrFail($request->integer('company_record_id'));
            return $this->json(true, 'Company detail.', ['company' => $this->companyPayload($request, $record->id, $record), 'license' => $record->licenseSnapshot()]);
        }
        $companies = Company::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Company $company) => $this->companyPayload($request, $company->id, $company));

        return $this->json(true, 'Company list.', ['companies' => $companies]);
    }

    public function license(Request $request)
    {
        $companyId = $this->companyId($request);
        $company = Company::find($companyId);

        return $this->json(true, 'License status.', [
            'company' => $this->companyPayload($request, $companyId),
            'license' => $company?->licenseSnapshot(),
        ]);
    }

    public function features(Request $request)
    {
        $companyId = $this->companyId($request);

        $features = Feature::query()
            ->orderBy('display_order')
            ->get()
            ->map(function (Feature $feature) use ($companyId) {
                $enabled = Schema::hasTable('company_features')
                    ? CompanyFeature::where('company_id', $companyId)->where('feature_key', $feature->key)->value('is_enabled')
                    : false;

                return [
                    'key' => $feature->key,
                    'name' => $feature->name,
                    'description' => $feature->description,
                    'is_enabled' => (bool) $enabled,
                ];
            });

        return $this->json(true, 'Feature list.', ['features' => $features]);
    }

    public function stores(Request $request)
    {
        $companyId = $this->companyId($request);
        $stores = Store::where('company_id', $companyId)->when(! $request->user()->isSuperAdmin() && $request->user()->store_id, fn ($q) => $q->whereKey($request->user()->store_id))->orderBy('name')->get(['id', 'name', 'code', 'company_id']);

        return $this->json(true, 'Store list.', ['stores' => $stores]);
    }

    public function categories(Request $request)
    {
        $companyId = $this->companyId($request);
        $categories = ProductCategory::where('company_id', $companyId)->orderBy('name')->get(['id', 'name', 'company_id']);

        return $this->json(true, 'Category list.', ['categories' => $categories]);
    }

    public function products(Request $request)
    {
        $companyId = $this->companyId($request);
        $query = Product::with(['category:id,name', 'tax:id,name,rate', 'product_variants'])
            ->where('company_id', $companyId)
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%')
                        ->orWhere('barcode', 'like', '%'.$search.'%')->orWhereHas('product_variants', fn ($variants) => $variants->where('name', 'like', '%'.$search.'%')->orWhere('sku', 'like', '%'.$search.'%')->orWhere('barcode', 'like', '%'.$search.'%'));
                });
            })
            ->when($request->barcode, fn ($q) => $q->where('barcode', $request->barcode))
            ->when($request->sku, fn ($q) => $q->where('sku', $request->sku))
            ->orderBy('name');

        if ($request->has('per_page')) {
            $products = $query->paginate(min((int) $request->get('per_page', 20), 100));
            $products->setCollection($products->getCollection()->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'price' => (float) $product->price,
                'cost' => (float) $product->cost,
                'image_url' => $product->image ? asset('admin/product_images/'.$product->image) : null,
                'minimum_stock' => (int) ($product->minimum_stock ?? 0),
                'variants' => $product->product_variants->map(fn ($variant) => ['id' => $variant->id, 'name' => $variant->name, 'sku' => $variant->sku, 'barcode' => $variant->barcode, 'price' => (float) $variant->price])->values(),
                'track_stock' => (bool) $product->track_stock,
                'stock_quantity' => (int) ($product->stock_quantity ?? 0),
                'category' => $product->category,
                'tax' => $product->tax,
            ]));

            return $this->json(true, 'Product list.', ['products' => $products]);
        }

        $products = $query->get()->map(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'price' => (float) $product->price,
            'cost' => (float) $product->cost,
                'image_url' => $product->image ? asset('admin/product_images/'.$product->image) : null,
                'minimum_stock' => (int) ($product->minimum_stock ?? 0),
                'variants' => $product->product_variants->map(fn ($variant) => ['id' => $variant->id, 'name' => $variant->name, 'sku' => $variant->sku, 'barcode' => $variant->barcode, 'price' => (float) $variant->price])->values(),
            'track_stock' => (bool) $product->track_stock,
            'stock_quantity' => (int) ($product->stock_quantity ?? 0),
            'category' => $product->category,
            'tax' => $product->tax,
        ]);

        return $this->json(true, 'Product list.', ['products' => $products]);
    }

    public function customers(Request $request)
    {
        $companyId = $this->companyId($request);
        $query = Customer::where('company_id', $companyId)
            ->when($request->search, function ($q, $search) {
                $q->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('customer_code', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name');

        $customers = $request->has('per_page')
            ? $query->paginate(min((int) $request->get('per_page', 20), 100))
            : $query->get();

        return $this->json(true, 'Customer list.', ['customers' => $customers]);
    }

    public function storeCustomer(Request $request)
    {
        $companyId = $this->companyId($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $customer = Customer::create($data + [
            'company_id' => $companyId,
            'customer_code' => 'CUST-'.now()->format('YmdHis').'-'.random_int(100, 999),
        ]);

        return $this->json(true, 'Customer berhasil dibuat.', ['customer' => $customer], 201);
    }

    public function paymentMethods(Request $request)
    {
        $companyId = $this->companyId($request);
        $methods = Method::where('company_id', $companyId)->orderBy('name')->get(['id', 'name', 'company_id']);

        return $this->json(true, 'Payment method list.', ['payment_methods' => $methods]);
    }

    private function companyId(Request $request): ?int
    {
        if ($request->user()?->isSuperAdmin() && $request->query('company_id')) {
            return (int) $request->query('company_id');
        }

        return $request->user()?->company_id;
    }

    private function companyPayload(Request $request, ?int $companyId, ?Company $loaded = null): ?array
    {
        $company = $loaded ?? Company::find($companyId);
        if (! $company) {
            return null;
        }

        return [
            'id' => $company->id,
            'name' => $company->name,
            'company_code' => $company->company_code,
            'legal_name' => $company->legal_name,
            'npwp' => $company->npwp,
            'email' => $company->email,
            'phone' => $company->phone,
            'address' => $company->address,
            'is_active' => (bool) $company->is_active,
            'created_at' => $company->created_at,
            'subscription_status' => $company->subscription_status,
            'payment_due_at' => $company->payment_due_at,
            'payment_amount' => $company->payment_amount,
            'grace_period_days' => $company->grace_period_days,
            'max_users' => $company->max_users,
        ];
    }

    private function json(bool $success, string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(compact('success', 'message', 'data'), $status);
    }
}
