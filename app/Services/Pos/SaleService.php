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
use App\Models\Pos\Payment;
use App\Models\Pos\ProductVariant;
use App\Models\Pos\StockMovement;
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
            $data = Sale::with(["store", "customer", "user", "sale_items", "company"])->with(['company']);
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('invoice', function($row) { return $row->invoice_number; })
                ->addColumn('date', function($row) { return $row->created_at ? $row->created_at->format("d M Y") : "-"; })
                ->addColumn('store', function($row) { return $row->store ? $row->store->name : "-"; })
                ->addColumn('customer', function($row) { return $row->customer ? $row->customer->name : "-"; })
                ->addColumn('cashier', function($row) { return "-"; })
                ->addColumn('items', function($row) { return $row->sale_items ? $row->sale_items->sum("quantity") : 0; })
                ->addColumn('subtotal', function($row) { return "Rp " . number_format($row->sub_total); })
                ->addColumn('discount', function($row) { return "Rp " . number_format($row->total_discount); })
                ->addColumn('tax', function($row) { return "Rp " . number_format($row->total_tax); })
                ->addColumn('grand_total', function($row) { return "Rp " . number_format($row->grand_total); })
                ->addColumn('paid', function($row) { return "Rp " . number_format($row->paid_amount); })
                ->addColumn('change', function($row) { return "Rp " . number_format($row->change_amount); })
                ->addColumn('payment_method', function($row) { return $row->payment_method; })
                ->addColumn('payment_status', function($row) { return $row->payment_status; })
                ->addColumn('sale_status', function($row) { return $row->sale_status; })
                ->addColumn('created', function($row) { return $row->created_at ? $row->created_at->format("H:i") : "-"; })
                ->addColumn('company', function($row) { return $row->company ? $row->company->name : "-"; })
                ->addColumn('created_by', function($row) { return "-"; })
                ->addColumn('created_at', function($row) { return $row->created_at ? $row->created_at->format("d M Y H:i") : "-"; })
                ->addColumn('action', function($row) { return '<a href="'.url('admin-panel/sale/edit/'.$row->id).'" class="nexora-action-btn is-warning"><i class="fas fa-edit"></i></a><a href="'.url('admin-panel/sale/delete/'.$row->id).'" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Delete?\')"><i class="fas fa-trash"></i></a>'; })
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
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
            return redirect()->back()->with('success', 'Sale cancelled successfully');
        }
        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    // POS selling screen
    public function pos()
    {
        $companyId = auth()->user()?->company_id;
        $categories = ProductCategory::when($companyId, function ($q) use ($companyId) { return $q->where('company_id', $companyId); })->get();
        $customers = Customer::when($companyId, function ($q) use ($companyId) { return $q->where('company_id', $companyId); })->get();
        $stores = Store::when($companyId, function ($q) use ($companyId) { return $q->where('company_id', $companyId); })->get();
        $methods = Method::when($companyId, function ($q) use ($companyId) { return $q->where('company_id', $companyId); })->get();
        $products = Product::with('tax','category','product_variants')->when($companyId, function ($q) use ($companyId) { return $q->where('company_id', $companyId); })->get();
        return view('admin.pos.sale.pos', compact('categories','products','customers','stores','methods'));
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

    // Calculate totals
    private function calculateTotals(array $cart)
    {
        $subtotal = 0; $tax = 0; $discount = 0; $total = 0;
        foreach($cart as $item){
            $lineBase = $item['price']*$item['quantity'];
            $lineDiscount = $item['discount']*$item['quantity'];
            $lineTax = (($lineBase - $lineDiscount) * ($item['tax_percent'] ?? 0))/100;
            $subtotal += $lineBase;
            $discount += $lineDiscount;
            $tax += $lineTax;
        }
        $total = $subtotal - $discount + $tax;
        return compact('subtotal','discount','tax','total');
    }

    // Checkout
    public function checkout(Request $request)
    {
        $cart = session()->get('pos_cart', []);
        if (empty($cart)) {
            return response()->json(['success'=>false,'message'=>'Cart is empty'],422);
        }

        $companyId = auth()->user()?->company_id;
        $storeId = $request->store_id ?: Store::where('company_id', $companyId)->first()?->id;
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
            $sale = DB::transaction(function () use ($cart, $totals, $storeId, $methodId, $userId, $request, $companyId) {
                $sale = new Sale();
                $sale->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
                $sale->company_id = $companyId;
                $sale->store_id = $storeId;
                $sale->customer_id = $request->customer_id ?: null;
                $sale->user_id = $userId;
                $sale->subtotal = $totals['subtotal'];
                $sale->tax = $totals['tax'];
                $sale->discount = $totals['discount'];
                $sale->total = $totals['total'];
                $sale->paid_amount = $request->paid_amount ?? $totals['total'];
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

                $paymentPayload = [
                    'company_id' => $companyId,
                    'sale_id' => $sale->id,
                    'method_id' => $methodId,
                    'amount' => $sale->paid_amount > 0 ? $sale->paid_amount : $totals['total'],
                    'reference' => 'PAY-' . $sale->invoice_number,
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
        $sale = Sale::with(['store', 'customer', 'user'])->where('company_id', $companyId)->findOrFail($id);
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
