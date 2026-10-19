<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Pos\CashierShift;
use App\Models\Pos\HeldBill;
use App\Models\Pos\Method;
use App\Models\Pos\Payment;
use App\Models\Pos\Product;
use App\Models\Pos\ProductVariant;
use App\Models\Pos\Sale;
use App\Models\Pos\SaleItem;
use App\Models\Pos\StockMovement;
use App\Models\Pos\Store;
use App\Models\Pos\Customer;
use App\Models\Pos\Tax;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PosController extends Controller
{
    public function checkout(Request $request)
    {
        if ($guard = $this->guardCompany($request, 'pos')) {
            return $guard;
        }

        $data = $request->validate([
            'store_id' => ['required', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.product_variant_id' => ['nullable', 'integer'],
            'items.*.name' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.is_custom_amount' => ['nullable', 'boolean'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method_id' => ['required', 'integer'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'idempotency_key' => ['nullable', 'string', 'max:120'],
        ]);

        $companyId = $this->companyId($request);
        $userId = $request->user()->id;
        $idempotencyKey = $data['idempotency_key'] ?? $request->header('Idempotency-Key');
        if ($idempotencyKey && Schema::hasColumn('sales', 'idempotency_key')) {
            $existingSale = Sale::with(['sale_items.product', 'payments.method', 'store', 'customer'])
                ->where('company_id', $companyId)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existingSale) {
                abort_unless((int) $existingSale->user_id === (int) $userId && (int) $existingSale->store_id === (int) $data['store_id'], 409, 'Idempotency key sudah digunakan oleh transaksi lain.');
                return $this->json(true, 'Checkout sudah pernah diproses.', [
                    'sale' => $existingSale,
                    'idempotent_replay' => true,
                ]);
            }
        }

        $store = Store::where('company_id', $companyId)->find($data['store_id']);
        if (! $store) {
            return $this->json(false, 'Store tidak valid untuk company ini.', null, 422);
        }

        if (! empty($data['customer_id']) && ! Customer::where('company_id', $companyId)->whereKey($data['customer_id'])->exists()) {
            return $this->json(false, 'Customer tidak valid untuk company ini.', null, 422);
        }

        $items = $this->normalizeItems($data['items'], $companyId);
        if (isset($items['error'])) {
            return $this->json(false, $items['error'], null, 422);
        }

        $payments = $this->normalizePayments($data['payments'], $companyId);
        if (isset($payments['error'])) {
            return $this->json(false, $payments['error'], null, 422);
        }

        $totals = $this->totals($items);
        $tenderedAmount = (float) collect($payments)->sum('amount');
        if ($tenderedAmount < $totals['total'] || (count($payments) > 1 && abs($tenderedAmount - $totals['total']) > 0.001) || (count($payments) === 1 && strtolower(trim((string) Method::find($payments[0]['method_id'])->name)) !== 'cash' && strtolower(trim((string) Method::find($payments[0]['method_id'])->name)) !== 'tunai' && abs($tenderedAmount - $totals['total']) > 0.001)) {
            return $this->json(false, 'Nominal pembayaran tidak sesuai total transaksi.', null, 422);
        }
        $paidAmount = (float) collect($payments)->reject(fn ($payment) => $payment['is_qris'])->sum('amount');
        $openShift = $this->currentOpenShift($companyId, $userId);

        try {
            $sale = DB::transaction(function () use ($items, $payments, $totals, $paidAmount, $companyId, $userId, $data, $openShift, $idempotencyKey) {
                $sale = new Sale();
                $sale->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
                $sale->company_id = $companyId;
                $sale->store_id = $data['store_id'];
                $sale->customer_id = $data['customer_id'] ?? null;
                $sale->user_id = $userId;
                if ($idempotencyKey && Schema::hasColumn('sales', 'idempotency_key')) {
                    $sale->idempotency_key = $idempotencyKey;
                }
                if (Schema::hasColumn('sales', 'cashier_shift_id')) {
                    $sale->cashier_shift_id = $openShift?->id;
                }
                $sale->subtotal = $totals['subtotal'];
                $sale->tax = $totals['tax'];
                $sale->discount = $totals['discount'];
                $sale->total = $totals['total'];
                $sale->paid_amount = $paidAmount;
                $sale->change_amount = max(0, $paidAmount - $totals['total']);
                $sale->status = $paidAmount >= $totals['total'] ? 'completed' : 'pending';
                $sale->payment_status = $paidAmount >= $totals['total'] ? 'PAID' : ($paidAmount > 0 ? 'PARTIAL' : 'UNPAID');
                $sale->save();

                foreach ($payments as $index => $payment) {
                    $payload = [
                        'company_id' => $companyId,
                        'sale_id' => $sale->id,
                        'method_id' => $payment['method_id'],
                        'amount' => $payment['amount'],
                        'reference' => 'PAY-'.$sale->invoice_number.'-'.($index + 1),
                    ];
                    if (Schema::hasColumn('payments', 'status')) {
                        $payload['status'] = $payment['is_qris'] ? 'pending' : 'active';
                    }
                    Payment::create($payload);
                }

                foreach ($items as $item) {
                    $product = Product::where('company_id', $companyId)->whereKey($item['product_id'])->lockForUpdate()->first();
                    if (! $product) {
                        throw new \RuntimeException('Produk tidak ditemukan.');
                    }

                    if ($product->track_stock && (float) $product->stock_quantity < (float) $item['quantity']) {
                        throw new \RuntimeException('Stok tidak cukup untuk '.$product->name.'.');
                    }

                    if ($product->track_stock) {
                        $product->decrement('stock_quantity', $item['quantity']);
                    }

                    $saleItemPayload = [
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'discount' => $item['discount'],
                        'tax' => $item['tax'],
                    ];
                    if (Schema::hasColumn('sale_items', 'company_id')) {
                        $saleItemPayload['company_id'] = $companyId;
                    }
                    SaleItem::create($saleItemPayload);

                    $movementPayload = [
                        'company_id' => $companyId,
                        'store_id' => $data['store_id'],
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'],
                        'quantity' => (string) (-1 * (float) $item['quantity']),
                        'type' => 'sale',
                    ];
                    if (Schema::hasColumn('stock_movements', 'status')) {
                        $movementPayload['status'] = 'active';
                    }
                    StockMovement::create($movementPayload);
                }

                return $sale->load(['sale_items.product', 'payments.method', 'store', 'customer']);
            });
        } catch (\Throwable $e) {
            if ($idempotencyKey && Schema::hasColumn('sales', 'idempotency_key')) {
                $replay = Sale::with(['sale_items.product', 'payments.method', 'store', 'customer'])->where('company_id', $companyId)->where('user_id', $userId)->where('store_id', $data['store_id'])->where('idempotency_key', $idempotencyKey)->first();
                if ($replay) return $this->json(true, 'Checkout sudah diproses.', ['sale' => $replay, 'idempotent_replay' => true]);
            }
            if ($e instanceof \Illuminate\Database\QueryException) return $this->json(false, 'Hasil checkout belum dapat dipastikan. Periksa riwayat dan referensi transaksi yang sama.', null, 503);
            return $this->json(false, $e->getMessage(), null, 422);
        }

        return $this->json(true, 'Checkout berhasil.', ['sale' => $sale], 201);
    }

    public function holdBills(Request $request)
    {
        if (! Schema::hasTable('held_bills')) {
            return $this->json(false, 'Migration held_bills belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        $companyId = $this->companyId($request);

        $bills = HeldBill::with(['store', 'customer'])
            ->where('company_id', $companyId)
            ->where('user_id', $request->user()->id)
            ->when(! $request->user()->isSuperAdmin() && $request->user()->store_id, fn ($q) => $q->where('store_id', $request->user()->store_id))
            ->where('status', 'held')
            ->latest()
            ->get();

        return $this->json(true, 'Held bill list.', ['held_bills' => $bills]);
    }

    public function storeHoldBill(Request $request)
    {
        if (! Schema::hasTable('held_bills')) {
            return $this->json(false, 'Migration held_bills belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        if ($guard = $this->guardCompany($request, 'pos')) {
            return $guard;
        }

        $data = $request->validate([
            'store_id' => ['required', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'bill_name' => ['nullable', 'string', 'max:255'],
            'table_name' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'totals' => ['nullable', 'array'],
        ]);

        $companyId = $this->companyId($request);
        if (! Store::where('company_id', $companyId)->whereKey($data['store_id'])->exists()) {
            return $this->json(false, 'Store tidak valid untuk company ini.', null, 422);
        }

        $bill = HeldBill::create([
            'company_id' => $companyId,
            'store_id' => $data['store_id'],
            'user_id' => $request->user()->id,
            'customer_id' => $data['customer_id'] ?? null,
            'bill_name' => $data['bill_name'] ?? 'Bill '.now()->format('H:i'),
            'table_name' => $data['table_name'] ?? null,
            'cart_snapshot' => $data['items'],
            'totals_snapshot' => $data['totals'] ?? null,
            'status' => 'held',
        ]);

        return $this->json(true, 'Held bill berhasil dibuat.', ['held_bill' => $bill], 201);
    }

    public function mergeHoldBills(Request $request)
    {
        if (! Schema::hasTable('held_bills')) {
            return $this->json(false, 'Migration held_bills belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        if ($guard = $this->guardCompany($request, 'pos')) {
            return $guard;
        }

        $data = $request->validate([
            'held_bill_ids' => ['required', 'array', 'min:2'],
            'held_bill_ids.*' => ['integer'],
            'bill_name' => ['nullable', 'string', 'max:255'],
            'table_name' => ['nullable', 'string', 'max:100'],
        ]);

        $companyId = $this->companyId($request);
        $bills = HeldBill::where('company_id', $companyId)
            ->where('user_id', $request->user()->id)
            ->where('status', 'held')
            ->whereIn('id', $data['held_bill_ids'])
            ->get();

        if ($bills->count() !== count(array_unique($data['held_bill_ids']))) {
            return $this->json(false, 'Ada held bill yang tidak valid atau sudah tidak aktif.', null, 422);
        }

        if ($bills->pluck('store_id')->unique()->count() !== 1 || $bills->pluck('customer_id')->unique()->count() !== 1) return $this->json(false, 'Gabungkan hanya bill dari toko dan customer yang sama.', null, 422);
        $merged = DB::transaction(function () use ($bills, $companyId, $request, $data) {
            $bills = HeldBill::where('company_id', $companyId)->where('user_id', $request->user()->id)->whereIn('id', $bills->pluck('id'))->where('status', 'held')->lockForUpdate()->get();
            abort_unless($bills->count() === count(array_unique($data['held_bill_ids'])), 409, 'Bill telah diproses oleh permintaan lain.');
            $items = $bills->flatMap(fn ($bill) => $bill->cart_snapshot ?? [])->values()->all();
            $total = $bills->sum(fn ($bill) => (float) data_get($bill->totals_snapshot, 'total', 0));

            $bills->each(fn ($bill) => $bill->forceFill(['status' => 'merged', 'restored_at' => now()])->save());

            return HeldBill::create([
                'company_id' => $companyId,
                'store_id' => $bills->first()->store_id,
                'user_id' => $request->user()->id,
                'customer_id' => $bills->first()->customer_id,
                'bill_name' => $data['bill_name'] ?? 'Merged Bill '.now()->format('H:i'),
                'table_name' => $data['table_name'] ?? $bills->pluck('table_name')->filter()->implode(' + '),
                'cart_snapshot' => $items,
                'totals_snapshot' => ['total' => $total],
                'status' => 'held',
            ]);
        });

        return $this->json(true, 'Held bill berhasil digabung.', ['held_bill' => $merged], 201);
    }

    public function restoreHoldBill(Request $request, int $id)
    {
        if (! Schema::hasTable('held_bills')) {
            return $this->json(false, 'Migration held_bills belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        $companyId = $this->companyId($request);
        $bill = HeldBill::where('company_id', $companyId)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['held', 'restored'])
            ->findOrFail($id);

        $bill->forceFill([
            'status' => 'restored',
            'restored_at' => now(),
        ])->save();

        return $this->json(true, 'Held bill berhasil direstore.', [
            'held_bill' => $bill->load('customer'),
            'items' => $bill->cart_snapshot,
            'totals' => $bill->totals_snapshot,
        ]);
    }

    public function deleteHoldBill(Request $request, int $id)
    {
        if (! Schema::hasTable('held_bills')) {
            return $this->json(false, 'Migration held_bills belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        $companyId = $this->companyId($request);
        HeldBill::where('company_id', $companyId)
            ->where('user_id', $request->user()->id)
            ->where('status', 'held')
            ->findOrFail($id)
            ->delete();

        return $this->json(true, 'Held bill berhasil dihapus.');
    }

    public function splitPreview(Request $request)
    {
        $data = $request->validate([
            'total' => ['required', 'numeric', 'min:0'],
            'splits' => ['required', 'array', 'min:2'],
            'splits.*.name' => ['nullable', 'string', 'max:100'],
            'splits.*.amount' => ['required', 'numeric', 'min:0'],
        ]);

        $splitTotal = collect($data['splits'])->sum(fn ($split) => (float) $split['amount']);
        $remaining = round((float) $data['total'] - $splitTotal, 2);

        return $this->json(true, 'Split bill preview.', [
            'total' => (float) $data['total'],
            'split_total' => $splitTotal,
            'remaining' => $remaining,
            'is_balanced' => abs($remaining) < 0.01,
            'splits' => $data['splits'],
        ]);
    }

    public function voidSale(Request $request, int $id)
    {
        if ($guard = $this->guardCompany($request, 'pos')) {
            return $guard;
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $companyId = $this->companyId($request);
        $sale = Sale::with(['sale_items.product', 'payments'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        if (in_array($sale->status, ['cancelled', 'void'], true)) {
            return $this->json(false, 'Sale sudah dibatalkan atau void.', null, 422);
        }

        $sale = DB::transaction(function () use ($sale, $data, $companyId, $request) {
            $sale = Sale::where('company_id', $companyId)->whereKey($sale->id)->lockForUpdate()->firstOrFail();
            abort_if(in_array(strtolower($sale->status), ['cancelled', 'void', 'voided'], true), 409, 'Invoice sudah dibatalkan.');
            $oldValue = $sale->only(['id', 'status', 'payment_status', 'total']);

            foreach ($sale->sale_items as $item) {
                $product = $item->product;
                if ($product && $product->track_stock) {
                    $product->increment('stock_quantity', $item->quantity);
                }

                $movementPayload = [
                    'company_id' => $companyId,
                    'store_id' => $sale->store_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => (string) ((float) $item->quantity),
                    'type' => 'sale_void',
                ];
                if (Schema::hasColumn('stock_movements', 'status')) {
                    $movementPayload['status'] = 'active';
                }
                StockMovement::create($movementPayload);
            }

            foreach ($sale->payments as $payment) {
                if (Schema::hasColumn('payments', 'status')) {
                    $payment->status = 'void';
                }
                if (Schema::hasColumn('payments', 'voided_at')) {
                    $payment->voided_at = now();
                }
                $payment->save();
            }

            $sale->status = 'void';
            $sale->payment_status = 'VOID';
            if (Schema::hasColumn('sales', 'cancelled_at')) {
                $sale->cancelled_at = now();
            }
            if (Schema::hasColumn('sales', 'void_reason')) {
                $sale->void_reason = $data['reason'];
            }
            if (Schema::hasColumn('sales', 'voided_by')) {
                $sale->voided_by = $request->user()->id;
            }
            $sale->save();

            AuditLogger::log('void', 'sales', $sale, $oldValue, $sale->only(['id', 'status', 'payment_status', 'void_reason', 'voided_by']));

            return $sale->fresh(['sale_items.product', 'payments.method']);
        });

        return $this->json(true, 'Sale berhasil divoid.', ['sale' => $sale]);
    }

    public function openShift(Request $request)
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return $this->json(false, 'Migration cashier_shifts belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        $data = $request->validate([
            'store_id' => ['required', 'integer'],
            'opening_cash' => ['nullable', 'numeric', 'min:0'],
            'shift_name' => ['nullable', 'string', 'max:100'],
        ]);

        $companyId = $this->companyId($request);
        $userId = $request->user()->id;

        if ($this->currentOpenShift($companyId, $userId)) {
            return $this->json(false, 'Masih ada shift yang sedang open.', null, 422);
        }

        $store = Store::where('company_id', $companyId)->find($data['store_id']);
        if (! $store) {
            return $this->json(false, 'Store tidak valid untuk company ini.', null, 422);
        }

        $shift = CashierShift::create([
            'company_id' => $companyId,
            'store_id' => $store->id,
            'user_id' => $userId,
            'shift_name' => $data['shift_name'] ?? $request->user()->shift_name,
            'opening_cash' => (float) ($data['opening_cash'] ?? 0),
            'opened_at' => now(),
            'status' => 'open',
        ])->load('store');

        return $this->json(true, 'Shift berhasil dibuka.', ['shift' => $shift], 201);
    }

    public function closeShift(Request $request)
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return $this->json(false, 'Migration cashier_shifts belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        $data = $request->validate([
            'closing_cash' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $companyId = $this->companyId($request);
        $shift = $this->currentOpenShift($companyId, $request->user()->id);
        if (! $shift) {
            return $this->json(false, 'Tidak ada shift yang sedang open.', null, 422);
        }

        if (Sale::where('company_id', $companyId)->where('cashier_shift_id', $shift->id)->where('status', 'pending')->exists()) return $this->json(false, 'Selesaikan atau void invoice pending sebelum menutup shift.', null, 422);
        $sales = Sale::where('company_id', $companyId)
            ->where('cashier_shift_id', $shift->id)
            ->where('status', 'completed')
            ->get();

        $salesTotal = (float) $sales->sum('total');
        $cashTotal = \App\Models\Pos\Payment::whereIn('sale_id', $sales->pluck('id'))->whereHas('method', fn ($q) => $q->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(TRIM(name))'), ['cash', 'tunai']))->where('status', 'active')->sum('amount');
        $expectedCash = (float) $shift->opening_cash + (float) $cashTotal - (float) $sales->sum('change_amount');
        $closingCash = (float) $data['closing_cash'];

        $shift->forceFill([
            'closing_cash' => $closingCash,
            'expected_cash' => $expectedCash,
            'cash_difference' => $closingCash - $expectedCash,
            'sales_count' => $sales->count(),
            'sales_total' => $salesTotal,
            'closed_at' => now(),
            'status' => 'closed',
            'note' => $data['note'] ?? null,
        ])->save();

        return $this->json(true, 'Shift berhasil ditutup.', ['shift' => $shift->fresh(['store'])]);
    }

    public function shiftReport(Request $request)
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return $this->json(false, 'Migration cashier_shifts belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        $companyId = $this->companyId($request);
        $shifts = CashierShift::with(['store', 'user'])
            ->where('company_id', $companyId)
            ->where('user_id', $request->user()->id)
            ->when($request->store_id, fn ($q) => $q->where('store_id', $request->store_id))
            ->when($request->date, fn ($q) => $q->whereDate('opened_at', $request->date))
            ->latest('opened_at')
            ->paginate((int) $request->get('per_page', 20));

                $active = $this->currentOpenShift($companyId, $request->user()->id);
        $summary = null;
        if ($active) {
            $sales = Sale::where('company_id', $companyId)->where('cashier_shift_id', $active->id)->where('status', 'completed')->get();
            $cash = Payment::whereIn('sale_id', $sales->pluck('id'))->where('status', 'active')->whereHas('method', fn ($q) => $q->whereIn(DB::raw('LOWER(TRIM(name))'), ['cash', 'tunai']))->sum('amount');
            $cash = (float) $cash - (float) $sales->sum('change_amount');
            $summary = ['sales_count' => $sales->count(), 'sales_total' => (float) $sales->sum('total'), 'cash_total' => $cash, 'opening_cash' => (float) $active->opening_cash, 'expected_cash' => (float) $active->opening_cash + $cash];
        }
        return $this->json(true, 'Shift report.', ['shifts' => $shifts, 'summary' => $summary]);
    }

    private function normalizeItems(array $rows, int $companyId): array
    {
        $items = [];

        foreach ($rows as $row) {
            $isCustom = (bool) ($row['is_custom_amount'] ?? false);
            $product = $isCustom
                ? $this->customAmountProduct($companyId)
                : Product::with('tax')->where('company_id', $companyId)->find($row['product_id'] ?? null);

            if (! $product) {
                return ['error' => 'Produk tidak ditemukan.'];
            }

            $variant = ! empty($row['product_variant_id']) ? ProductVariant::whereKey($row['product_variant_id'])->where('product_id', $product->id)->first() : null;
            if (! empty($row['product_variant_id']) && ! $variant) {
                return ['error' => 'Variant tidak valid untuk '.$product->name.'.'];
            }

            $quantity = (float) $row['quantity'];
            $price = $isCustom ? (float) ($row['price'] ?? 0) : (float) ($variant?->price ?? $product->price);
            $discount = (float) ($row['discount'] ?? 0);
            if ($discount > $price) return ['error' => 'Diskon melebihi harga item.'];
            if ($discount > 0) abort_unless(app(\App\Services\MobileAccess::class)->allows(request()->user(), 'pos.discount'), 403, 'Anda tidak memiliki izin diskon.');
            $taxPercent = (float) ($product->tax->rate ?? 0);
            $lineBase = $price * $quantity;
            $lineDiscount = $discount * $quantity;
            $lineTax = round(($lineBase - $lineDiscount) * $taxPercent / 100, 0);

            $items[] = [
                'product_id' => $product->id,
                'product_variant_id' => $row['product_variant_id'] ?? null,
                'quantity' => $quantity,
                'price' => $price,
                'discount' => $discount,
                'tax' => $lineTax,
            ];
        }

        return $items;
    }

    private function normalizePayments(array $rows, int $companyId): array
    {
        $payments = [];
        if (count(array_unique(array_column($rows, 'method_id'))) !== count($rows)) return ['error' => 'Metode pembayaran tidak boleh duplikat.'];

        foreach ($rows as $row) {
            if (! Method::where('company_id', $companyId)->whereKey($row['method_id'])->exists()) {
                return ['error' => 'Metode pembayaran tidak tersedia untuk company ini.'];
            }

            $payments[] = [
                'is_qris' => strtolower(trim((string) Method::where('company_id', $companyId)->find($row['method_id'])->name)) === 'qris',
                'method_id' => (int) $row['method_id'],
                'amount' => (float) $row['amount'],
            ];
        }

        return $payments;
    }

    private function totals(array $items): array
    {
        return [
            'subtotal' => collect($items)->sum(fn ($item) => $item['price'] * $item['quantity']),
            'discount' => collect($items)->sum(fn ($item) => $item['discount'] * $item['quantity']),
            'tax' => collect($items)->sum('tax'),
            'total' => collect($items)->sum(fn ($item) => ($item['price'] * $item['quantity']) - ($item['discount'] * $item['quantity']) + $item['tax']),
        ];
    }

    private function customAmountProduct(int $companyId): Product
    {
        $tax = Tax::firstOrCreate(['company_id' => $companyId, 'name' => 'No Tax'], ['rate' => 0]);

        $payload = [
            'tax_id' => $tax->id,
            'name' => 'Custom Amount',
            'barcode' => 'CUSTOM-AMOUNT-'.$companyId,
            'price' => 0,
            'cost' => 0,
            'track_stock' => false,
        ];

        if (Schema::hasColumn('products', 'stock_quantity')) {
            $payload['stock_quantity'] = 0;
        }

        if (Schema::hasColumn('products', 'minimum_stock')) {
            $payload['minimum_stock'] = 0;
        }

        return Product::withTrashed()->firstOrCreate(
            ['company_id' => $companyId, 'sku' => 'CUSTOM-AMOUNT-'.$companyId],
            $payload
        );
    }

    private function currentOpenShift(int $companyId, int $userId): ?CashierShift
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return null;
        }

        return CashierShift::with('store')
            ->where('company_id', $companyId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();
    }

    private function guardCompany(Request $request, string $featureKey)
    {
        $company = Company::find($this->companyId($request));
        if (! $company) {
            return $this->json(false, 'Company tidak ditemukan.', null, 422);
        }

        if (! $request->user()?->isSuperAdmin() && $company->isLicenseLocked()) {
            return $this->json(false, 'License company sedang locked atau expired.', ['license' => $company->licenseSnapshot()], 423);
        }

        if (! $request->user()?->isSuperAdmin() && Schema::hasTable('company_features')) {
            $enabled = CompanyFeature::where('company_id', $company->id)
                ->where('feature_key', $featureKey)
                ->where('is_enabled', true)
                ->exists();

            if (! $enabled) {
                return $this->json(false, 'Fitur tidak aktif untuk company ini.', null, 403);
            }
        }

        return null;
    }

    private function companyId(Request $request): int
    {
        if ($request->user()?->isSuperAdmin() && $request->query('company_id')) {
            return (int) $request->query('company_id');
        }

        return (int) $request->user()?->company_id;
    }

    private function json(bool $success, string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(compact('success', 'message', 'data'), $status);
    }
}
