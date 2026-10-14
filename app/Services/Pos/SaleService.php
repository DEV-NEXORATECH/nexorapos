<?php
namespace App\Services\Pos;

use Illuminate\Http\Request;
use App\Models\Pos\Sale;
use App\Models\Pos\SaleItem;
use App\Models\Pos\Product;
use App\Models\Pos\ProductCategory;
use App\Models\Pos\Store;
use App\Models\Pos\Customer;
use App\Models\Pos\Method;
use App\Models\Pos\Tax;
use App\Models\Pos\Payment;
use App\Models\Pos\ProductVariant;
use App\Models\Pos\StockMovement;
use App\Models\Pos\CashierShift;
use App\Models\Pos\HeldBill;
use App\Models\User;
use App\Pos\Sale\SaleStoreValidation;
use App\Support\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaleService
{
    public $saleValidator;

    public function __construct()
    {
        $this->saleValidator = new SaleStoreValidation();
    }

    // Listing all sales
    public function index()
    {
        if (request()->ajax()) {
            $data = Sale::with(["store", "customer", "user", "sale_items", "payments.method", "company"]);
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('invoice', function($row) { return $row->invoice_number; })
                ->addColumn('date', function($row) { return $row->created_at ? $row->created_at->format("d M Y") : "-"; })
                ->addColumn('store', function($row) { return $row->store ? $row->store->name : "-"; })
                ->addColumn('customer', function($row) { return $row->customer ? $row->customer->name : "-"; })
                ->addColumn('cashier', function($row) { return "-"; })
                ->addColumn('items', function($row) { return $row->sale_items ? $row->sale_items->sum("quantity") : 0; })
                ->addColumn('subtotal', function($row) { return "Rp " . number_format((float) $row->subtotal, 0, ',', '.'); })
                ->addColumn('discount', function($row) { return "Rp " . number_format((float) $row->discount, 0, ',', '.'); })
                ->addColumn('tax', function($row) { return "Rp " . number_format((float) $row->tax, 0, ',', '.'); })
                ->addColumn('grand_total', function($row) { return "Rp " . number_format((float) $row->total, 0, ',', '.'); })
                ->addColumn('paid', function($row) { return "Rp " . number_format((float) $row->paid_amount, 0, ',', '.'); })
                ->addColumn('change', function($row) { return "Rp " . number_format((float) $row->change_amount, 0, ',', '.'); })
                ->addColumn('payment_method', function($row) { return $row->payments->map(fn ($payment) => $payment->method?->name)->filter()->unique()->implode(', ') ?: '-'; })
                ->addColumn('payment_status', function($row) { return $row->payment_status; })
                ->addColumn('sale_status', function($row) { return $row->status; })
                ->addColumn('created', function($row) { return $row->created_at ? $row->created_at->format("H:i") : "-"; })
                ->addColumn('company', function($row) { return $row->company ? $row->company->name : "-"; })
                ->addColumn('created_by', function($row) { return "-"; })
                ->addColumn('created_at', function($row) { return $row->created_at ? $row->created_at->format("d M Y H:i") : "-"; })
                ->addColumn('action', function($row) { return '<a href="'.url('admin-panel/pos/invoice/'.$row->id).'" class="nexora-action-btn is-info" title="View invoice"><i class="fas fa-eye"></i></a>'; })
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pos.sale.listing');
    }

    public function reconciliation(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $companyId = auth()->user()?->company_id;

        $sales = Sale::with(['store', 'customer', 'user'])
            ->where('company_id', $companyId)
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $payments = Payment::whereHas('sale', fn ($query) => $query->whereDate('created_at', $date))
            ->get();

        $summary = [
            'sales_count' => $sales->count(),
            'gross_sales' => $sales->sum('subtotal'),
            'discount' => $sales->sum('discount'),
            'tax' => $sales->sum('tax'),
            'net_sales' => $sales->sum('total'),
            'payment_total' => $payments->sum('amount'),
        ];
        $summary['difference'] = $summary['net_sales'] - $summary['payment_total'];

        $paymentsByMethod = $payments
            ->groupBy(fn ($payment) => $payment->method?->name ?? 'Unknown')
            ->map(fn ($items, $method) => [
                'method' => $method,
                'count' => $items->count(),
                'amount' => $items->sum('amount'),
            ])
            ->values();

        $hourlySales = Sale::selectRaw('HOUR(created_at) as hour, SUM(total) as total')
            ->whereDate('created_at', $date)
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get();

        return view('admin.pos.sale.reconciliation', compact('date', 'sales', 'summary', 'paymentsByMethod', 'hourlySales'));
    }

    public function shiftReport(Request $request)
    {
        $companyId = $this->currentCompanyId($request);
        $date = $request->get('date');

        if (! $companyId) {
            return redirect()->back()->with('failed', 'Company tidak ditemukan.');
        }

        if (! Schema::hasTable('cashier_shifts')) {
            return view('admin.pos.shifts.report', [
                'date' => $date,
                'summary' => [
                    'total_shift' => 0,
                    'open_shift' => 0,
                    'closed_shift' => 0,
                    'sales_total' => 0,
                    'cash_difference' => 0,
                ],
                'shifts' => collect(),
                'migrationMissing' => true,
            ]);
        }

        $query = CashierShift::with(['store', 'user'])
            ->where('company_id', $companyId)
            ->when($date, fn ($q) => $q->whereDate('opened_at', $date))
            ->latest('opened_at');

        $allShifts = (clone $query)->get();
        $summary = [
            'total_shift' => $allShifts->count(),
            'open_shift' => $allShifts->where('status', 'open')->count(),
            'closed_shift' => $allShifts->where('status', 'closed')->count(),
            'sales_total' => $allShifts->sum('sales_total'),
            'cash_difference' => $allShifts->sum('cash_difference'),
        ];

        $shifts = $query->paginate(20)->withQueryString();

        return view('admin.pos.shifts.report', compact('date', 'summary', 'shifts') + ['migrationMissing' => false]);
    }

    // Add sale (non-POS)
    public function add()
    {
        $companyId = auth()->user()?->company_id;
        $stores = Store::where('company_id', $companyId)->get();
        $customers = Customer::where('company_id', $companyId)->get();
        $users = User::where('company_id', $companyId)->get();
        return view('admin.pos.sale.add', compact('stores', 'customers', 'users'));
    }

    // Store normal sale
    public function store(Request $request)
    {
        try {
            $inputs = $request->all();
            $errorMessage = $this->saleValidator->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $subtotal = (float) ($request->subtotal ?? 0);
            $tax = (float) ($request->tax ?? 0);
            $discount = (float) ($request->discount ?? 0);
            $companyId = auth()->user()?->company_id;

            $sale = new Sale();
            if (! Store::whereKey($request->store_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Store tidak valid untuk company ini.')->withInput();
            }

            if ($request->customer_id && ! Customer::whereKey($request->customer_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Customer tidak valid untuk company ini.')->withInput();
            }

            $sale->company_id = $companyId;
            $sale->store_id = $request->store_id;
            $sale->customer_id = $request->customer_id ?: null;
            $sale->user_id = auth()->id() ?: $request->user_id;
            $sale->subtotal = $subtotal;
            $sale->tax = $tax;
            $sale->discount = $discount;
            $sale->total = max(0, $subtotal - $discount + $tax);
            $sale->status = $request->status;
            $sale->save();

            return redirect()->to('admin-panel/sales/listing')->with('success', 'Record Added Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    // Edit sale
    public function edit($id)
    {
        $companyId = auth()->user()?->company_id;
        $stores = Store::where('company_id', $companyId)->get();
        $customers = Customer::where('company_id', $companyId)->get();
        $users = User::where('company_id', $companyId)->get();
        $sale = Sale::where('id', $id)->where('company_id', $companyId)->first();

        if ($sale) {
            return view('admin.pos.sale.update', compact('stores', 'customers', 'users', 'sale'));
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    // Update sale
    public function update(Request $request, $id)
    {
        try {
            $companyId = auth()->user()?->company_id;
            $sale = Sale::where('id', $id)->where('company_id', $companyId)->first();
            if (!$sale) return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');

            $inputs = $request->all();
            $errorMessage = $this->saleValidator->validate($inputs);
            if ($errorMessage) return redirect()->back()->with('failed', $errorMessage);

            $subtotal = (float) ($request->subtotal ?? 0);
            $tax = (float) ($request->tax ?? 0);
            $discount = (float) ($request->discount ?? 0);

            if (! Store::whereKey($request->store_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Store tidak valid untuk company ini.')->withInput();
            }

            if ($request->customer_id && ! Customer::whereKey($request->customer_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Customer tidak valid untuk company ini.')->withInput();
            }

            $sale->store_id = $request->store_id;
            $sale->customer_id = $request->customer_id ?: null;
            $sale->user_id = auth()->id() ?: $request->user_id;
            $sale->subtotal = $subtotal;
            $sale->tax = $tax;
            $sale->discount = $discount;
            $sale->total = max(0, $subtotal - $discount + $tax);
            $sale->status = $request->status;
            $sale->save();

            return redirect()->to('admin-panel/sales/listing')->with('success', 'Record Updated Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    // Delete sale
    public function destroy($id)
    {
        $companyId = auth()->user()?->company_id;
        $sale = Sale::with(['payments'])->where('company_id', $companyId)->find($id);
        if ($sale) {
            $this->cancelSale($sale);
            $isCashier = strtolower((string) auth()->user()?->role?->role) === 'kasir';
            $destination = $isCashier ? 'admin-panel/pos/sell' : 'admin-panel/sales/listing';
            return redirect()->to($destination)->with('success', 'Sale cancelled successfully');
        }
        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    // POS selling screen
    public function pos()
    {
        $companyId = $this->currentCompanyId();
        $assignedStoreId = auth()->user()?->store_id;
        $categories = ProductCategory::when($companyId, function ($q) use ($companyId) { return $q->where('company_id', $companyId); })->get();
        $customers = Customer::when($companyId, function ($q) use ($companyId) { return $q->where('company_id', $companyId); })->get();
        $stores = Store::when($companyId, function ($q) use ($companyId) { return $q->where('company_id', $companyId); })
            ->when($assignedStoreId, function ($q) use ($assignedStoreId) { return $q->where('id', $assignedStoreId); })
            ->get();

        // Existing companies may have been created before payment methods were
        // seeded. Ensure QRIS is available in the POS checkout automatically.
        if ($companyId) {
            Method::firstOrCreate(['company_id' => $companyId, 'name' => 'QRIS']);
        }

        $methods = Method::when($companyId, function ($q) use ($companyId) { return $q->where('company_id', $companyId); })->get();
        $products = Product::with('tax','category','product_variants')->when($companyId, function ($q) use ($companyId) { return $q->where('company_id', $companyId); })->get();
        $openShift = $this->currentOpenShift($companyId);
        $heldBills = Schema::hasTable('held_bills')
            ? HeldBill::with(['store', 'customer'])
                ->where('company_id', $companyId)
                ->where('user_id', auth()->id())
                ->where('status', 'held')
                ->latest()
                ->limit(10)
                ->get()
            : collect();

        return view('admin.pos.sale.pos', compact('categories','products','customers','stores','methods','assignedStoreId','openShift','heldBills'));
    }

    // Add to cart
    public function addToCart(Request $request)
    {
        try {
            $cart = session()->get('pos_cart', []);
            $productId = (int)$request->product_id;
            $variantId = $request->product_variant_id ? (int)$request->product_variant_id : null;
            $quantity = max(1, (int)($request->quantity ?? 1));

            $product = Product::with('tax','category')->findOrFail($productId);
            if ($variantId && ! ProductVariant::whereKey($variantId)->where('product_id', $product->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'Variant tidak valid untuk produk ini.'], 422);
            }
            $key = $variantId ? $productId.'-'.$variantId : (string)$productId;

            $price = (float)($product->price ?? 0);
            $discount = max(0, (float)($request->discount ?? 0));
            $taxPercent = (float)($product->tax->rate ?? 0);

            if (isset($cart[$key])) {
                $cart[$key]['quantity'] += $quantity;
            } else {
                $cart[$key] = [
                    'product_id' => $productId,
                    'product_name' => $product->name ?? 'Product',
                    'category_name' => optional($product->category)->name,
                    'product_variant_id' => $variantId,
                    'price' => $price,
                    'discount' => $discount,
                    'tax_percent' => $taxPercent,
                    'quantity' => $quantity,
                ];
            }

            session(['pos_cart' => $cart]);
            $totals = $this->calculateTotals($cart);
            $this->syncToCfd($cart, $totals);
            return response()->json(['success'=>true, 'cart'=>$cart, 'totals'=>$totals]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

    public function addCustomAmount(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $companyId = $this->currentCompanyId($request);
        if (! $companyId) {
            return response()->json(['success' => false, 'message' => 'Company tidak ditemukan.'], 422);
        }

        $product = $this->customAmountProduct($companyId);
        $amount = (float) $request->amount;
        $name = $request->filled('name') ? $request->name : 'Custom Amount';
        $cart = session()->get('pos_cart', []);
        $key = 'custom-'.uniqid();

        $cart[$key] = [
            'product_id' => $product->id,
            'product_name' => $name,
            'category_name' => 'Custom',
            'product_variant_id' => null,
            'price' => $amount,
            'discount' => 0,
            'tax_percent' => 0,
            'quantity' => 1,
            'is_custom_amount' => true,
        ];

        session(['pos_cart' => $cart]);
        $totals = $this->calculateTotals($cart);
        $this->syncToCfd($cart, $totals);

        return response()->json(['success' => true, 'cart' => $cart, 'totals' => $totals]);
    }

    // Update cart
    public function updateCart(Request $request)
    {
        try {
            $cart = session()->get('pos_cart', []);
            $key = (string)$request->key;
            if (!isset($cart[$key])) return response()->json(['success'=>false,'message'=>'Item not found'],404);

            if ($request->has('quantity')) $cart[$key]['quantity'] = max(1,(int)$request->quantity);
            if ($request->has('discount')) $cart[$key]['discount'] = max(0,(float)$request->discount);

            session(['pos_cart'=>$cart]);
            $totals = $this->calculateTotals($cart);
            $this->syncToCfd($cart, $totals);
            return response()->json(['success'=>true, 'cart'=>$cart, 'totals'=>$totals]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()],500);
        }
    }

    // Remove cart item
    public function removeFromCart(Request $request)
    {
        try {
            $cart = session()->get('pos_cart', []);
            $key = (string)$request->key;
            if(isset($cart[$key])) {
                unset($cart[$key]);
                session(['pos_cart'=>$cart]);
            }
            return response()->json(['success'=>true,'cart'=>$cart,'totals'=>$this->calculateTotals($cart)]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()],500);
        }
    }

    public function holdBill(Request $request)
    {
        if (! Schema::hasTable('held_bills')) {
            return response()->json(['success' => false, 'message' => 'Migration held bills belum dijalankan. Jalankan php artisan migrate.'], 422);
        }

        $cart = session()->get('pos_cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Cart masih kosong.'], 422);
        }

        $companyId = $this->currentCompanyId($request);
        $storeId = $this->selectedStoreId($request, $companyId);
        if (! $storeId) {
            return response()->json(['success' => false, 'message' => 'Store belum tersedia.'], 422);
        }

        $bill = HeldBill::create([
            'company_id' => $companyId,
            'store_id' => $storeId,
            'user_id' => auth()->id() ?: $request->user_id,
            'customer_id' => $request->customer_id ?: null,
            'bill_name' => $request->bill_name ?: 'Bill '.now()->format('H:i'),
            'cart_snapshot' => $cart,
            'totals_snapshot' => $this->calculateTotals($cart),
            'status' => 'held',
        ]);

        session()->forget('pos_cart');
        $this->syncToCfd([], $this->calculateTotals([]));

        return response()->json([
            'success' => true,
            'message' => 'Bill berhasil disimpan.',
            'bill' => $bill,
            'cart' => [],
            'totals' => $this->calculateTotals([]),
        ]);
    }

    public function restoreHeldBill(Request $request, $id)
    {
        if (! Schema::hasTable('held_bills')) {
            return response()->json(['success' => false, 'message' => 'Migration held bills belum dijalankan.'], 422);
        }

        try {
            $companyId = $this->currentCompanyId($request);
            $userId = auth()->id() ?: $request->user_id;

            $query = HeldBill::query()
                ->where('company_id', $companyId)
                ->where('user_id', $userId);

            // Older installations may have the table but not this column.
            if (Schema::hasColumn('held_bills', 'status')) {
                $query->where('status', 'held');
            }

            $bill = $query->find($id);
            if (! $bill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Held bill tidak ditemukan, sudah direstore, atau bukan milik kasir ini.',
                ], 404);
            }

            $cart = $bill->cart_snapshot;
            if (is_string($cart)) {
                $cart = json_decode($cart, true);
            }
            if (! is_array($cart) || empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Isi held bill kosong atau format cart tidak valid.',
                ], 422);
            }

            // Normalize snapshots created by older POS versions. Those
            // snapshots may not contain discount/tax fields yet.
            $cart = $this->normalizeCart($cart);

            session(['pos_cart' => $cart]);

            if (Schema::hasColumn('held_bills', 'status')) {
                $bill->status = 'restored';
            }
            if (Schema::hasColumn('held_bills', 'restored_at')) {
                $bill->restored_at = now();
            }
            $bill->save();

            $totals = $this->calculateTotals($cart);
            $this->syncToCfd($cart, $totals);

            return response()->json(['success' => true, 'cart' => $cart, 'totals' => $totals]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Restore bill gagal diproses. Cek log Laravel untuk detailnya.',
            ], 500);
        }
    }

    public function deleteHeldBill(Request $request, $id)
    {
        if (! Schema::hasTable('held_bills')) {
            return response()->json(['success' => false, 'message' => 'Migration held bills belum dijalankan.'], 422);
        }

        $companyId = $this->currentCompanyId($request);
        $bill = HeldBill::where('company_id', $companyId)
            ->where('user_id', auth()->id() ?: $request->user_id)
            ->where('status', 'held')
            ->findOrFail($id);

        $bill->delete();

        return response()->json(['success' => true]);
    }

    public function openShift(Request $request)
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return response()->json(['success' => false, 'message' => 'Migration shift belum dijalankan. Jalankan php artisan migrate.'], 422);
        }

        $request->validate([
            'store_id' => ['required', 'exists:stores,id'],
            'opening_cash' => ['nullable', 'numeric', 'min:0'],
            'shift_name' => ['nullable', 'string', 'max:100'],
        ]);

        $companyId = $this->currentCompanyId($request);
        $userId = auth()->id() ?: $request->user_id;

        if ($this->currentOpenShift($companyId, $userId)) {
            return response()->json(['success' => false, 'message' => 'Masih ada shift yang sedang open. Tutup shift dulu.'], 422);
        }

        $store = Store::where('company_id', $companyId)->find($request->store_id);
        if (! $store) {
            return response()->json(['success' => false, 'message' => 'Store tidak valid untuk company ini.'], 422);
        }

        $shift = CashierShift::create([
            'company_id' => $companyId,
            'store_id' => $store->id,
            'user_id' => $userId,
            'shift_name' => $request->shift_name ?: auth()->user()?->shift_name,
            'opening_cash' => (float) ($request->opening_cash ?? 0),
            'opened_at' => now(),
            'status' => 'open',
        ]);

        return response()->json(['success' => true, 'shift' => $shift->load('store')]);
    }

    public function closeShift(Request $request)
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return response()->json(['success' => false, 'message' => 'Migration shift belum dijalankan. Jalankan php artisan migrate.'], 422);
        }

        $request->validate([
            'closing_cash' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $companyId = $this->currentCompanyId($request);
        $shift = $this->currentOpenShift($companyId, auth()->id() ?: $request->user_id);
        if (! $shift) {
            return response()->json(['success' => false, 'message' => 'Tidak ada shift yang sedang open.'], 422);
        }

        $sales = Sale::where('company_id', $companyId)
            ->where('cashier_shift_id', $shift->id)
            ->where('status', 'completed')
            ->get();

        $salesTotal = (float) $sales->sum('total');
        $expectedCash = (float) $shift->opening_cash + $salesTotal;
        $closingCash = (float) $request->closing_cash;

        $shift->forceFill([
            'closing_cash' => $closingCash,
            'expected_cash' => $expectedCash,
            'cash_difference' => $closingCash - $expectedCash,
            'sales_count' => $sales->count(),
            'sales_total' => $salesTotal,
            'closed_at' => now(),
            'status' => 'closed',
            'note' => $request->note,
        ])->save();

        return response()->json(['success' => true, 'shift' => $shift->fresh(['store'])]);
    }

    // Calculate totals
    private function calculateTotals(array $cart)
    {
        $subtotal = 0; $tax = 0; $discount = 0; $total = 0;
        foreach($cart as $item){
            $price = (float) ($item['price'] ?? 0);
            $quantity = max(0, (float) ($item['quantity'] ?? 0));
            $itemDiscount = max(0, (float) ($item['discount'] ?? 0));
            $taxPercent = max(0, (float) ($item['tax_percent'] ?? 0));
            $lineBase = $price * $quantity;
            $lineDiscount = $itemDiscount * $quantity;
            $lineTax = (($lineBase - $lineDiscount) * $taxPercent)/100;
            $subtotal += $lineBase;
            $discount += $lineDiscount;
            $tax += $lineTax;
        }
        $total = $subtotal - $discount + $tax;
        return compact('subtotal','discount','tax','total');
    }

    private function normalizeCart(array $cart): array
    {
        return collect($cart)->map(function ($item) {
            $item = is_array($item) ? $item : [];
            $item['product_id'] = (int) ($item['product_id'] ?? 0);
            $item['product_variant_id'] = ! empty($item['product_variant_id'])
                ? (int) $item['product_variant_id']
                : null;
            $item['quantity'] = max(1, (int) ($item['quantity'] ?? 1));
            $item['price'] = max(0, (float) ($item['price'] ?? 0));
            $item['discount'] = max(0, (float) ($item['discount'] ?? 0));
            $item['tax_percent'] = max(0, (float) ($item['tax_percent'] ?? 0));
            $item['product_name'] = $item['product_name'] ?? 'Product';

            return $item;
        })->all();
    }

    // Checkout
    public function checkout(Request $request)
    {
        $cart = $this->normalizeCart(session()->get('pos_cart', []));
        if (empty($cart)) {
            return response()->json(['success'=>false,'message'=>'Cart is empty'],422);
        }

        $companyId = $this->currentCompanyId($request);
        $assignedStoreId = auth()->user()?->store_id;
        $storeId = $request->store_id ?: ($assignedStoreId ?: Store::where('company_id', $companyId)->first()?->id);
        $store = $storeId ? Store::where('id', $storeId)->where('company_id', $companyId)->first() : null;
        if (! $store) {
            return response()->json(['success'=>false,'message'=>'Store tidak tersedia. Buat store terlebih dahulu.'],422);
        }

        $methodId = $request->method_id ?: Method::where('company_id', $companyId)->first()?->id;
        $method = $methodId ? Method::where('id', $methodId)->where('company_id', $companyId)->first() : null;
        if (! $method) {
            return response()->json(['success'=>false,'message'=>'Metode pembayaran tidak tersedia.'],422);
        }

        if ($request->customer_id && ! Customer::whereKey($request->customer_id)->where('company_id', $companyId)->exists()) {
            return response()->json(['success'=>false,'message'=>'Customer tidak valid untuk company ini.'],422);
        }

        $userId = auth()->id() ?? $request->user_id ?? null;
        if (! $userId) {
            return response()->json(['success'=>false,'message'=>'User tidak terautentikasi.'],401);
        }

        $totals = $this->calculateTotals($cart);
        $paymentRows = $this->normalizePaymentRows($request, $companyId, $totals['total']);
        if (isset($paymentRows['error'])) {
            return response()->json(['success' => false, 'message' => $paymentRows['error']], 422);
        }
        $paidAmount = collect($paymentRows)->sum('amount');
        $openShift = $this->currentOpenShift($companyId, auth()->id() ?: $request->user_id);

        foreach ($cart as $item) {
            $product = Product::where('id', $item['product_id'])->where('company_id', $companyId)->first();
            if (! $product) {
                return response()->json(['success'=>false,'message'=>'Produk tidak ditemukan.'],422);
            }
            if (! empty($item['product_variant_id']) && ! ProductVariant::whereKey($item['product_variant_id'])->where('product_id', $product->id)->exists()) {
                return response()->json(['success'=>false,'message'=>'Variant tidak valid untuk '.$product->name.'.'],422);
            }
            if ($product->track_stock && (float) $product->stock_quantity < (float) $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak cukup untuk '.$product->name.'. Tersedia: '.$product->stock_quantity,
                ], 422);
            }
        }

        try {
            $sale = DB::transaction(function () use ($cart, $totals, $storeId, $userId, $request, $companyId, $paymentRows, $paidAmount, $openShift) {
                $sale = new Sale();
                $sale->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
                $sale->company_id = $companyId;
                $sale->store_id = $storeId;
                $sale->customer_id = $request->customer_id ?: null;
                $sale->user_id = $userId;
                if (Schema::hasColumn('sales', 'cashier_shift_id')) {
                    $sale->cashier_shift_id = $openShift?->id;
                }
                $sale->subtotal = $totals['subtotal'];
                $sale->tax = $totals['tax'];
                $sale->discount = $totals['discount'];
                $sale->total = $totals['total'];
                $sale->paid_amount = $paidAmount;
                $sale->change_amount = max(0, $sale->paid_amount - $totals['total']);
                $sale->status = 'completed';
                
                if ($sale->paid_amount >= $sale->total) {
                    $sale->payment_status = 'PAID';
                } elseif ($sale->paid_amount > 0) {
                    $sale->payment_status = 'PARTIAL';
                } else {
                    $sale->payment_status = 'UNPAID';
                }
                
                $sale->save();

                foreach ($paymentRows as $index => $paymentRow) {
                    $paymentPayload = [
                        'company_id' => $companyId,
                        'sale_id' => $sale->id,
                        'method_id' => $paymentRow['method_id'],
                        'amount' => $paymentRow['amount'],
                        'reference' => 'PAY-' . $sale->invoice_number . '-' . ($index + 1),
                    ];
                    if (Schema::hasColumn('payments', 'status')) {
                        $paymentPayload['status'] = 'active';
                    }
                    if (Schema::hasColumn('payments', 'created_by')) {
                        $paymentPayload['created_by'] = $userId;
                    }
                    if (Schema::hasColumn('payments', 'transaction_type')) {
                        $paymentPayload['transaction_type'] = 'sale';
                    }
                    Payment::create($paymentPayload);
                }

                foreach ($cart as $item) {
                    $product = Product::whereKey($item['product_id'])->where('company_id', $companyId)->lockForUpdate()->first();

                    if ($product && $product->track_stock) {
                        $product->decrement('stock_quantity', $item['quantity']);
                    }

                    $lineBase = (float) $item['price'] * (float) $item['quantity'];
                    $lineDiscount = (float) $item['discount'] * (float) $item['quantity'];
                    $lineTax = round(($lineBase - $lineDiscount) * (float) ($item['tax_percent'] ?? 0) / 100, 2);

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'discount' => $item['discount'],
                        'tax' => $lineTax,
                    ]);

                    $movementPayload = [
                        'company_id' => $companyId,
                        'store_id' => $storeId,
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'],
                        'quantity' => (string) (-1 * (int) $item['quantity']),
                        'type' => 'sale',
                    ];
                    if (Schema::hasColumn('stock_movements', 'status')) {
                        $movementPayload['status'] = 'active';
                    }
                    StockMovement::create($movementPayload);
                }

                return $sale;
            });
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()],500);
        }

        session()->forget('pos_cart');
        $this->syncToCfd([], $this->calculateTotals([]));
        return response()->json(['success'=>true,'sale_id'=>$sale->id,'redirect'=>url('admin-panel/pos/invoice/'.$sale->id)]);
    }

    private function normalizePaymentRows(Request $request, ?int $companyId, float $total): array
    {
        $rows = collect($request->input('payments', []))
            ->map(function ($row) {
                return [
                    'method_id' => isset($row['method_id']) ? (int) $row['method_id'] : null,
                    'amount' => isset($row['amount']) ? (float) $row['amount'] : 0,
                ];
            })
            ->filter(fn ($row) => $row['method_id'] && $row['amount'] > 0)
            ->values();

        if ($rows->isEmpty()) {
            $methodId = $request->method_id ?: Method::where('company_id', $companyId)->first()?->id;
            $amount = (float) ($request->paid_amount ?? $total);
            $rows = collect([[
                'method_id' => $methodId,
                'amount' => $amount > 0 ? $amount : $total,
            ]]);
        }

        foreach ($rows as $row) {
            if (! Method::where('id', $row['method_id'])->where('company_id', $companyId)->exists()) {
                return ['error' => 'Metode pembayaran tidak tersedia untuk company ini.'];
            }
        }

        return $rows->all();
    }

    private function currentCompanyId(?Request $request = null): ?int
    {
        return auth()->user()?->company_id
            ?? session('company_id')
            ?? ($request?->user_id ? User::whereKey($request->user_id)->value('company_id') : null);
    }

    private function selectedStoreId(Request $request, ?int $companyId): ?int
    {
        $assignedStoreId = auth()->user()?->store_id;
        $storeId = $request->store_id ?: ($assignedStoreId ?: Store::where('company_id', $companyId)->first()?->id);

        return $storeId && Store::where('id', $storeId)->where('company_id', $companyId)->exists()
            ? (int) $storeId
            : null;
    }

    private function currentOpenShift(?int $companyId, ?int $userId = null): ?CashierShift
    {
        $userId ??= auth()->id();

        if (! $companyId || ! $userId || ! Schema::hasTable('cashier_shifts')) {
            return null;
        }

        return CashierShift::with('store')
            ->where('company_id', $companyId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();
    }

    private function customAmountProduct(int $companyId): Product
    {
        if (! Schema::hasTable('taxes') || ! Schema::hasTable('products')) {
            abort(422, 'Migration produk/tax belum lengkap.');
        }

        $tax = Tax::firstOrCreate(
            ['company_id' => $companyId, 'name' => 'No Tax'],
            ['rate' => 0]
        );

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

        $product = Product::withTrashed()->firstOrCreate(
            ['company_id' => $companyId, 'sku' => 'CUSTOM-AMOUNT-'.$companyId],
            $payload
        );

        if (method_exists($product, 'trashed') && $product->trashed()) {
            $product->restore();
        }

        return $product;
    }

    public function cancelSale(Sale $sale): void
    {
        if (in_array($sale->status, ['cancelled', 'void'], true)) {
            return;
        }

        DB::transaction(function () use ($sale) {
            $oldValue = $sale->only(['id', 'status', 'total']);
            $companyId = $sale->company_id;

            foreach ($sale->sale_items as $item) {
                $product = $item->product;
                if ($product?->track_stock) {
                    $product->increment('stock_quantity', (int) $item->quantity);
                }

                $movementPayload = [
                    'company_id' => $companyId,
                    'store_id' => $sale->store_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => (string) abs((int) $item->quantity),
                    'type' => 'sale_cancel',
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

            $sale->status = 'cancelled';
            if (Schema::hasColumn('sales', 'cancelled_at')) {
                $sale->cancelled_at = now();
            }
            $sale->save();

            AuditLogger::log('cancel', 'sales', $sale, $oldValue, $sale->only(['id', 'status', 'total']));
        });
    }

    // Show invoice
    public function invoice($id)
    {
        $companyId = auth()->user()?->company_id;
        $sale = Sale::with([
            'store',
            'customer',
            'user',
            'payments.method',
            'sale_items.product',
            'sale_items.product_variant',
        ])
            // Company users remain tenant-isolated. Super Admin has no
            // company_id, so do not accidentally search for company_id = NULL.
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->findOrFail($id);
        return view('admin.pos.sale.invoice', compact('sale'));
    }
    // Generate PDF invoice
    public function pdf($id)
    {
        $companyId = auth()->user()?->company_id;
        $sale = Sale::with([
            'store',
            'customer',
            'user',
            'payments.method',
            'sale_items.product',
            'sale_items.product_variant'
        ])->where('company_id', $companyId)->findOrFail($id);

        $pdf = Pdf::loadView('admin.pos.sale.invoice_pdf', compact('sale'))
                  ->setPaper('a4', 'portrait')
                  ->setOption('fontDir', public_path('fonts/'))
                  ->setOption('fontCache', public_path('fonts/'))
                  // If using remote assets, enable them:
                  ->setOption('isRemoteEnabled', true);

        return $pdf->download('invoice_'.$sale->id.'.pdf');
    }

    private function syncToCfd($cart, $totals)
    {
        $userId = auth()->id();
        if ($userId) {
            \Illuminate\Support\Facades\Cache::put("cfd_cart_{$userId}", [
                'cart' => $cart,
                'totals' => $totals,
                'updated_at' => now()->toIso8601String()
            ], now()->addDay());
        }
    }
}
