<?php
namespace App\Services\Pos;

use Illuminate\Http\Request;
use App\Models\POS\Sale;
use App\Models\POS\SaleItem;
use App\Models\POS\Product;
use App\Models\POS\ProductCategory;
use App\Models\Pos\Store;
use App\Models\Pos\Customer;
use App\Models\User;
use App\Pos\Sale\SaleStoreValidation;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $sales = Sale::with('store', 'user', 'customer')->get();
        return view('admin.pos.sale.listing', compact('sales'));
    }

    // Add sale (non-POS)
    public function add()
    {
        $stores = Store::all();
        $customers = Customer::all();
        $users = User::all();
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

            $sale = new Sale();
            $sale->store_id = $request->store_id;
            $sale->customer_id = $request->customer_id;
            $sale->user_id = $request->user_id;
            $sale->subtotal = $request->subtotal;
            $sale->tax = $request->tax;
            $sale->discount = $request->discount;
            $sale->total = $request->total;
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
        $stores = Store::all();
        $customers = Customer::all();
        $users = User::all();
        $sale = Sale::find($id);

        if ($sale) {
            return view('admin.pos.sale.update', compact('stores', 'customers', 'users', 'sale'));
        }

        return redirect()->back()->with('failed', 'Record Not Found');
    }

    // Update sale
    public function update(Request $request, $id)
    {
        try {
            $sale = Sale::find($id);
            if (!$sale) return redirect()->back()->with('failed', 'Record Not Found');

            $inputs = $request->all();
            $errorMessage = $this->saleValidator->validate($inputs);
            if ($errorMessage) return redirect()->back()->with('failed', $errorMessage);

            $sale->store_id = $request->store_id;
            $sale->customer_id = $request->customer_id;
            $sale->user_id = $request->user_id;
            $sale->subtotal = $request->subtotal;
            $sale->tax = $request->tax;
            $sale->discount = $request->discount;
            $sale->total = $request->total;
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
        $sale = Sale::find($id);
        if ($sale) {
            $sale->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }
        return redirect()->back()->with('failed', 'Record Not Found');
    }

    // POS selling screen
    public function pos()
    {
        $categories = ProductCategory::all();
        $customers = Customer::all();
        $stores = Store::all();
        $products = Product::with('tax','category','product_variants')->get();
        return view('admin.pos.sale.pos', compact('categories','products','customers','stores'));
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
            $key = $variantId ? $productId.'-'.$variantId : (string)$productId;

            $price = (float)($request->price ?? $product->price ?? 0);
            $discount = (float)($request->discount ?? 0);
            $taxPercent = (float)($product->tax->percentage ?? 0);

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
            return response()->json(['success'=>true, 'cart'=>$cart, 'totals'=>$this->calculateTotals($cart)]);
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
            if ($request->has('discount')) $cart[$key]['discount'] = (float)$request->discount;

            session(['pos_cart'=>$cart]);
            return response()->json(['success'=>true,'cart'=>$cart,'totals'=>$this->calculateTotals($cart)]);
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
        try {
            $cart = session()->get('pos_cart', []);
            if(empty($cart)) return response()->json(['success'=>false,'message'=>'Cart is empty'],422);

            $totals = $this->calculateTotals($cart);

            $sale = new Sale();
            $sale->store_id = $request->store_id ?? Store::first()?->id;
            $sale->customer_id = $request->customer_id ?? null; // allow walk-in
            $sale->user_id = auth()->id() ?? $request->user_id ?? null;
            $sale->subtotal = $totals['subtotal'];
            $sale->tax = $totals['tax'];
            $sale->discount = $totals['discount'];
            $sale->total = $totals['total'];
            $sale->status = 'completed';
            $sale->save();

            foreach($cart as $item){
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'tax' => $item['tax_percent'],
                ]);
            }

            session()->forget('pos_cart');
            return response()->json(['success'=>true,'sale_id'=>$sale->id,'redirect'=>url('admin-panel/pos/invoice/'.$sale->id)]);
        } catch(\Exception $e){
            return response()->json(['success'=>false,'message'=>$e->getMessage()],500);
        }
    }

    // Show invoice
    public function invoice($id)
    {
        $sale = Sale::with(['store','customer','user','sale_items.product','sale_items.product_variant'])->findOrFail($id);
        return view('admin.pos.sale.invoice', compact('sale'));
    }
    // Generate PDF invoice
    public function pdf($id)
    {
        $sale = Sale::with([
            'store',
            'customer',
            'user',
            'sale_items.product',
            'sale_items.product_variant'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.pos.sale.invoice_pdf', compact('sale'))
                  ->setPaper('a4', 'portrait')
                  ->setOption('fontDir', public_path('fonts/'))
                  ->setOption('fontCache', public_path('fonts/'))
                  // If using remote assets, enable them:
                  ->setOption('isRemoteEnabled', true);

        return $pdf->download('invoice_'.$sale->id.'.pdf');
    }
}
