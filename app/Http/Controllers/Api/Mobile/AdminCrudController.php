<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Pos\Customer;
use App\Models\Pos\Discount;
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
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminCrudController extends Controller
{
    private function resources(): array
    {
        return [
        'stores' => [
            'model' => Store::class,
            'feature' => 'pos',
            'relations' => [],
            'rules' => [
                'name' => ['required', 'string', 'max:255'],
                'code' => ['required', 'string', 'max:100'],
            ],
        ],
        'categories' => [
            'model' => ProductCategory::class,
            'feature' => 'products',
            'relations' => [],
            'rules' => [
                'name' => ['required', 'string', 'max:255'],
            ],
        ],
        'taxes' => [
            'model' => Tax::class,
            'feature' => 'products',
            'relations' => [],
            'rules' => [
                'name' => ['required', 'string', 'max:255'],
                'rate' => ['required', 'numeric', 'min:0'],
            ],
        ],
        'products' => [
            'model' => Product::class,
            'feature' => 'products',
            'relations' => ['category', 'tax', 'product_variants'],
            'rules' => [
                'name' => ['required', 'string', 'max:255'],
                'sku' => ['required', 'string', 'max:255'],
                'barcode' => ['required', 'string', 'max:255'],
                'category_id' => ['nullable', 'integer'],
                'tax_id' => ['required', 'integer'],
                'price' => ['required', 'numeric', 'min:0'],
                'cost' => ['required', 'numeric', 'min:0'],
                'track_stock' => ['nullable', 'boolean'],
                'stock_quantity' => ['nullable', 'integer', 'min:0'],
                'minimum_stock' => ['nullable', 'integer', 'min:0'],
            ],
        ],
        'product-variants' => [
            'model' => ProductVariant::class,
            'feature' => 'products',
            'relations' => ['product'],
            'rules' => [
                'product_id' => ['required', 'integer'],
                'name' => ['required', 'string', 'max:255'],
                'sku' => ['required', 'string', 'max:255'],
                'barcode' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric', 'min:0'],
                'cost' => ['required', 'numeric', 'min:0'],
            ],
        ],
        'suppliers' => [
            'model' => Supplier::class,
            'feature' => 'suppliers',
            'relations' => [],
            'rules' => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'address' => ['nullable', 'string', 'max:1000'],
            ],
        ],
        'customers' => [
            'model' => Customer::class,
            'feature' => 'customers',
            'relations' => [],
            'rules' => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'address' => ['nullable', 'string', 'max:1000'],
                'customer_code' => ['nullable', 'string', 'max:100'],
            ],
        ],
        'discounts' => [
            'model' => Discount::class,
            'feature' => 'sales',
            'relations' => [],
            'rules' => [
                'name' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', Rule::in(['percentage', 'fixed'])],
                'value' => ['required', 'numeric', 'min:0'],
            ],
        ],
        'payment-methods' => [
            'model' => Method::class,
            'feature' => 'payments',
            'relations' => [],
            'rules' => [
                'name' => ['required', 'string', 'max:255'],
            ],
        ],
        'purchases' => [
            'model' => Purchase::class,
            'feature' => 'suppliers',
            'relations' => ['supplier', 'purchase_items.product'],
            'rules' => [
                'supplier_id' => ['required', 'integer'],
                'reference' => ['nullable', 'string', 'max:255'],
                'total_amount' => ['required', 'numeric', 'min:0'],
                'status' => ['nullable', 'string', 'max:50'],
            ],
        ],
        'purchase-items' => [
            'model' => PurchaseItem::class,
            'feature' => 'suppliers',
            'relations' => ['purchase', 'product'],
            'rules' => [
                'purchase_id' => ['required', 'integer'],
                'product_id' => ['required', 'integer'],
                'quantity' => ['required', 'numeric', 'min:0.01'],
                'cost' => ['required', 'numeric', 'min:0'],
            ],
        ],
        'sales' => [
            'model' => Sale::class,
            'feature' => 'sales',
            'relations' => ['store', 'customer', 'user', 'sale_items.product', 'payments.method'],
            'rules' => [
                'store_id' => ['required', 'integer'],
                'customer_id' => ['nullable', 'integer'],
                'user_id' => ['nullable', 'integer'],
                'subtotal' => ['nullable', 'numeric', 'min:0'],
                'tax' => ['nullable', 'numeric', 'min:0'],
                'discount' => ['nullable', 'numeric', 'min:0'],
                'total' => ['nullable', 'numeric', 'min:0'],
                'paid_amount' => ['nullable', 'numeric', 'min:0'],
                'change_amount' => ['nullable', 'numeric', 'min:0'],
                'status' => ['nullable', 'string', 'max:50'],
                'payment_status' => ['nullable', 'string', 'max:50'],
                'invoice_number' => ['nullable', 'string', 'max:255'],
            ],
        ],
        'sale-items' => [
            'model' => SaleItem::class,
            'feature' => 'sales',
            'relations' => ['sale', 'product', 'product_variant'],
            'rules' => [
                'sale_id' => ['required', 'integer'],
                'product_id' => ['required', 'integer'],
                'product_variant_id' => ['nullable', 'integer'],
                'quantity' => ['required', 'numeric', 'min:0.01'],
                'price' => ['required', 'numeric', 'min:0'],
                'discount' => ['nullable', 'numeric', 'min:0'],
                'tax' => ['nullable', 'numeric', 'min:0'],
            ],
        ],
        'payments' => [
            'model' => Payment::class,
            'feature' => 'payments',
            'relations' => ['sale', 'method'],
            'rules' => [
                'sale_id' => ['required', 'integer'],
                'method_id' => ['required', 'integer'],
                'amount' => ['required', 'numeric', 'min:0.01'],
                'reference' => ['nullable', 'string', 'max:255'],
                'status' => ['nullable', 'string', 'max:50'],
            ],
        ],
        'stock-movements' => [
            'model' => StockMovement::class,
            'feature' => 'products',
            'relations' => ['store', 'product', 'product_variant'],
            'rules' => [
                'store_id' => ['required', 'integer'],
                'product_id' => ['required', 'integer'],
                'product_variant_id' => ['nullable', 'integer'],
                'quantity' => ['required', 'numeric', 'min:0.01'],
                'type' => ['required', 'string', 'max:50'],
                'status' => ['nullable', 'string', 'max:50'],
                'note' => ['nullable', 'string', 'max:1000'],
            ],
        ],
        ];
    }

    public function index(Request $request, string $resource)
    {
        [$config, $companyId] = $this->authorizeResource($request, $resource);
        $model = $config['model'];
        $query = $model::query()->with($config['relations']);

        if (Schema::hasColumn((new $model())->getTable(), 'company_id')) {
            $query->where('company_id', $companyId);
        }

        if ($request->search) {
            $this->applySearch($query, $model, (string) $request->search);
        }

        return $this->json(true, "{$resource} list.", [
            $resource => $query->latest('id')->paginate((int) $request->get('per_page', 20)),
        ]);
    }

    public function show(Request $request, string $resource, int $id)
    {
        [$config, $companyId] = $this->authorizeResource($request, $resource);
        $record = $this->findRecord($config, $companyId, $id);

        return $this->json(true, "{$resource} detail.", ['record' => $record]);
    }

    public function store(Request $request, string $resource)
    {
        [$config, $companyId] = $this->authorizeResource($request, $resource);
        $data = $this->validatedData($request, $config, $companyId);
        $model = $config['model'];
        $record = new $model();
        $record->fill($data);

        if (Schema::hasColumn($record->getTable(), 'company_id')) {
            $record->company_id = $companyId;
        }

        if ($record instanceof Sale && ! $record->invoice_number) {
            $record->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
        }

        if ($record instanceof Sale && ! $record->user_id) {
            $record->user_id = $request->user()->id;
        }

        if ($record instanceof Customer && ! $record->customer_code) {
            $record->customer_code = 'CUST-'.now()->format('YmdHis').'-'.random_int(100, 999);
        }

        $record->save();

        if ($record instanceof StockMovement) {
            $this->applyStockMovement($record, $companyId, $this->signedQuantity($record->quantity, $record->type));
        }

        return $this->json(true, "{$resource} berhasil dibuat.", ['record' => $record->fresh($config['relations'])], 201);
    }

    public function update(Request $request, string $resource, int $id)
    {
        [$config, $companyId] = $this->authorizeResource($request, $resource);
        $record = $this->findRecord($config, $companyId, $id);
        $data = $this->validatedData($request, $config, $companyId, true);

        $oldStockDelta = $record instanceof StockMovement
            ? $this->signedQuantity($record->quantity, $record->type)
            : null;
        $oldProductId = $record instanceof StockMovement ? $record->product_id : null;

        $record->fill($data);
        $record->save();

        if ($record instanceof StockMovement) {
            if ((int) $oldProductId !== (int) $record->product_id) {
                $oldProduct = Product::where('company_id', $companyId)->find($oldProductId);
                if ($oldProduct) {
                    $this->adjustProductStock($oldProduct, -$oldStockDelta);
                }
                $this->applyStockMovement($record, $companyId, $this->signedQuantity($record->quantity, $record->type));
            } else {
                $newDelta = $this->signedQuantity($record->quantity, $record->type);
                $this->applyStockMovement($record, $companyId, $newDelta - $oldStockDelta);
            }
        }

        return $this->json(true, "{$resource} berhasil diupdate.", ['record' => $record->fresh($config['relations'])]);
    }

    public function destroy(Request $request, string $resource, int $id)
    {
        [$config, $companyId] = $this->authorizeResource($request, $resource);
        $record = $this->findRecord($config, $companyId, $id);

        if ($message = $this->deleteBlockReason($record)) {
            return $this->json(false, $message, null, 422);
        }

        if ($record instanceof StockMovement) {
            $this->applyStockMovement($record, $companyId, -$this->signedQuantity($record->quantity, $record->type));
        }

        $record->delete();

        return $this->json(true, "{$resource} berhasil dihapus.");
    }

    private function authorizeResource(Request $request, string $resource): array
    {
        $resources = $this->resources();
        abort_if(! isset($resources[$resource]), 404, 'Resource API tidak ditemukan.');

        $config = $resources[$resource];
        $companyId = $this->companyId($request);

        abort_if(! $companyId, 422, 'Company tidak ditemukan.');
        $company = Company::find($companyId);
        abort_if(! $company, 422, 'Company tidak ditemukan.');

        if (! $request->user()?->isSuperAdmin()) {
            abort_if($company->isLicenseLocked(), 423, 'License company sedang locked atau expired.');

            if (Schema::hasTable('company_features')) {
                $enabled = CompanyFeature::where('company_id', $companyId)
                    ->where('feature_key', $config['feature'])
                    ->where('is_enabled', true)
                    ->exists();

                abort_if(! $enabled, 403, 'Fitur tidak aktif untuk company ini.');
            }
        }

        return [$config, $companyId];
    }

    private function findRecord(array $config, int $companyId, int $id): Model
    {
        $model = $config['model'];
        $query = $model::query()->with($config['relations']);

        if (Schema::hasColumn((new $model())->getTable(), 'company_id')) {
            $query->where('company_id', $companyId);
        }

        return $query->findOrFail($id);
    }

    private function validatedData(Request $request, array $config, int $companyId, bool $partial = false): array
    {
        $rules = $config['rules'];
        if ($partial) {
            $rules = collect($rules)->map(function ($rule) {
                $rule = is_array($rule) ? $rule : explode('|', $rule);
                return collect($rule)
                    ->reject(fn ($item) => $item === 'required')
                    ->prepend('sometimes')
                    ->all();
            })->all();
        }

        $data = $request->validate($rules);
        $this->validateRelations($data, $companyId);

        return collect($data)
            ->filter(fn ($value) => $value !== null)
            ->all();
    }

    private function validateRelations(array $data, int $companyId): void
    {
        $checks = [
            'store_id' => Store::class,
            'category_id' => ProductCategory::class,
            'tax_id' => Tax::class,
            'product_id' => Product::class,
            'supplier_id' => Supplier::class,
            'customer_id' => Customer::class,
            'method_id' => Method::class,
            'sale_id' => Sale::class,
            'purchase_id' => Purchase::class,
            'user_id' => User::class,
        ];

        foreach ($checks as $field => $model) {
            if (! array_key_exists($field, $data) || ! $data[$field]) {
                continue;
            }

            $record = $model::query()
                ->when(Schema::hasColumn((new $model())->getTable(), 'company_id'), fn ($q) => $q->where('company_id', $companyId))
                ->whereKey($data[$field])
                ->exists();

            abort_if(! $record, 422, "{$field} tidak valid untuk company ini.");
        }

        if (! empty($data['product_variant_id'])) {
            $valid = ProductVariant::whereKey($data['product_variant_id'])
                ->when(! empty($data['product_id']), fn ($q) => $q->where('product_id', $data['product_id']))
                ->where('company_id', $companyId)
                ->exists();

            abort_if(! $valid, 422, 'product_variant_id tidak valid untuk company ini.');
        }
    }

    private function deleteBlockReason(Model $record): ?string
    {
        if ($record instanceof Product && ($record->sale_items()->exists() || $record->purchase_items()->exists())) {
            return 'Product sudah dipakai transaksi. Gunakan inactive/soft delete, jangan hard delete.';
        }

        if ($record instanceof ProductCategory && $record->products()->exists()) {
            return 'Category masih dipakai product.';
        }

        if ($record instanceof Supplier && $record->purchases()->exists()) {
            return 'Supplier masih dipakai purchase.';
        }

        if ($record instanceof Method && $record->Payments()->exists()) {
            return 'Payment method masih dipakai payment.';
        }

        return null;
    }

    private function applySearch($query, string $model, string $search): void
    {
        $table = (new $model())->getTable();
        $query->where(function ($q) use ($table, $search) {
            foreach (['name', 'code', 'sku', 'barcode', 'email', 'phone', 'reference', 'invoice_number'] as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $q->orWhere($column, 'like', '%'.$search.'%');
                }
            }
        });
    }

    private function companyId(Request $request): ?int
    {
        if ($request->user()?->isSuperAdmin()) {
            return (int) ($request->query('company_id') ?: $request->input('company_id') ?: $request->user()?->company_id);
        }

        return $request->user()?->company_id;
    }

    private function signedQuantity($quantity, $type): int
    {
        $quantity = (int) $quantity;

        return in_array($type, ['out', 'sale', 'purchase_cancel'], true) ? -abs($quantity) : abs($quantity);
    }

    private function applyStockMovement(StockMovement $movement, int $companyId, int $delta): void
    {
        $product = Product::where('company_id', $companyId)->find($movement->product_id);
        if (! $product) {
            return;
        }

        if ($delta < 0 && $product->track_stock && (int) $product->stock_quantity + $delta < 0) {
            abort(422, 'Insufficient Stock. Available: '.$product->stock_quantity);
        }

        $this->adjustProductStock($product, $delta);
    }

    private function adjustProductStock(Product $product, int $delta): void
    {
        if (! $product->track_stock) {
            return;
        }

        $product->stock_quantity = max(0, (int) $product->stock_quantity + $delta);
        $product->save();
    }

    private function json(bool $success, string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(compact('success', 'message', 'data'), $status);
    }
}
