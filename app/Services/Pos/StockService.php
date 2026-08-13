<?php
namespace App\Services\Pos;
use Illuminate\Http\Request;
use App\Models\Pos\Store;
use App\Models\Pos\Product;
use App\Models\Pos\ProductVariant;
use App\Models\Pos\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Pos\Stock\StockStoreValidation;
use App\Support\AuditLogger;
class StockService{
    public $stock;
    public function __construct()
    {
        return $this->stock=new StockStoreValidation();
    }
    public function index()
    {
        $companyId = auth()->user()?->company_id;
        $stocks = StockMovement::with('store', 'product', 'product_variant')
            ->where('company_id', $companyId)
            ->get();
        return view('admin.pos.stock.listing', compact('stocks'));
    }

    public function add()
    {
        $companyId = auth()->user()?->company_id;
        $stores = Store::where('company_id', $companyId)->get();
        $products = Product::where('company_id', $companyId)->get();
        $variants = ProductVariant::whereHas('product', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->get();
        return view('admin.pos.stock.add', compact('stores', 'products', 'variants'));
    }

    public function store(Request $request)
    {
        try{
            $companyId = auth()->user()?->company_id;
            $inputs=$request->all();
            $errorMessage=$this->stock->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }

            $product=Product::where('company_id', $companyId)->find($request->product_id);
            if(! $product){
                return redirect()->back()->with('failed', 'Product Not Found or Unauthorized');
            }
            if ($request->product_variant_id && ! ProductVariant::whereKey($request->product_variant_id)->where('product_id', $product->id)->exists()) {
                return redirect()->back()->with('failed', 'Variant tidak valid untuk produk ini.');
            }

            $signedQuantity=$this->signedQuantity($request->quantity, $request->type);
            if($signedQuantity < 0 && $product->track_stock && (int) $product->stock_quantity + $signedQuantity < 0){
                return redirect()->back()->with('failed', 'Insufficient Stock. Available: '.$product->stock_quantity);
            }

            $stock=DB::transaction(function () use ($request, $product, $signedQuantity, $companyId) {
                $stock=new StockMovement();
                $stock->company_id = $companyId;
                $stock->store_id=$request->store_id;
                $stock->product_id=$request->product_id;
                $stock->product_variant_id=$request->product_variant_id ?: null;
                $stock->quantity=$request->quantity;
                $stock->type=$request->type;
                if (Schema::hasColumn('stock_movements', 'status')) {
                    $stock->status='active';
                }
                $stock->save();

                $this->adjustStock($product, $signedQuantity);
                return $stock;
            });

            return redirect()->to('admin-panel/stock/listing')->with('success', 'Record Added Successfully');
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $companyId = auth()->user()?->company_id;
        $stores = Store::where('company_id', $companyId)->get();
        $products = Product::where('company_id', $companyId)->get();
        $variants = ProductVariant::whereHas('product', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->get();
        $stock = StockMovement::with('store', 'product', 'product_variant')
            ->where('company_id', $companyId)
            ->find($id);
        if($stock)
        {
            return view('admin.pos.stock.update', compact('stock', 'stores', 'products', 'variants'));
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    public function update(Request $request, $id)
    {
        try{
            $companyId = auth()->user()?->company_id;
            $stock = StockMovement::where('company_id', $companyId)->find($id);
            if($stock)
            {
                $inputs=$request->all();
                $errorMessage=$this->stock->validate($inputs);
                if($errorMessage){
                    return redirect()->back()->with('failed', $errorMessage);
                }

                $product=Product::where('company_id', $companyId)->find($request->product_id ?: $stock->product_id);
                if(! $product){
                    return redirect()->back()->with('failed', 'Product Not Found or Unauthorized');
                }
                if ($request->product_variant_id && ! ProductVariant::whereKey($request->product_variant_id)->where('product_id', $product->id)->exists()) {
                    return redirect()->back()->with('failed', 'Variant tidak valid untuk produk ini.');
                }

                $newSigned=$this->signedQuantity($request->quantity, $request->type);
                $oldSigned=$this->signedQuantity($stock->quantity, $stock->type);
                if($newSigned < 0 && $product->track_stock && (int) $product->stock_quantity - $oldSigned + $newSigned < 0){
                    return redirect()->back()->with('failed', 'Insufficient Stock. Available: '.((int) $product->stock_quantity - $oldSigned));
                }

                DB::transaction(function () use ($stock, $request, $product, $newSigned, $oldSigned, $companyId) {
                    if((int) $stock->product_id === (int) $request->product_id){
                        $this->adjustStock($product, $newSigned - $oldSigned);
                    }else{
                        $oldProduct=Product::where('company_id', $companyId)->find($stock->product_id);
                        if($oldProduct){
                            $this->adjustStock($oldProduct, -$oldSigned);
                        }
                        $this->adjustStock($product, $newSigned);
                    }

                    $stock->store_id=$request->store_id;
                    $stock->product_id=$request->product_id;
                    $stock->product_variant_id=$request->product_variant_id ?: null;
                    $stock->quantity=$request->quantity;
                    $stock->type=$request->type;
                    $stock->save();
                });

                return redirect()->to('admin-panel/stock/listing')->with('success', 'Record Updated Successfully');
            }

            return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
        }catch(\Exception $e){
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $companyId = auth()->user()?->company_id;
        $stock = StockMovement::where('company_id', $companyId)->find($id);
        if($stock){
            if (($stock->status ?? 'active') === 'void') {
                return redirect()->back()->with('success', 'Stock movement already voided.');
            }
            $product=Product::where('company_id', $companyId)->find($stock->product_id);
            if($product){
                $this->adjustStock($product, -$this->signedQuantity($stock->quantity, $stock->type));
            }
            $oldValue = $stock->only(['id', 'product_id', 'quantity', 'type', 'status']);
            if (Schema::hasColumn('stock_movements', 'status')) {
                $stock->status='void';
            }
            if (Schema::hasColumn('stock_movements', 'voided_at')) {
                $stock->voided_at=now();
            }
            $stock->save();
            AuditLogger::log('void', 'stock_movements', $stock, $oldValue, $stock->only(['id', 'product_id', 'quantity', 'type', 'status']));
            return redirect()->back()->with('success', 'Stock movement voided successfully');
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    private function signedQuantity($quantity, $type)
    {
        $quantity=(int) $quantity;
        return in_array($type, ['out', 'sale', 'purchase_cancel'], true) ? -abs($quantity) : abs($quantity);
    }

    private function adjustStock(?Product $product, $delta)
    {
        if(! $product || ! $product->track_stock){
            return;
        }
        $product->stock_quantity=max(0, (int) $product->stock_quantity + (int) $delta);
        $product->save();
    }
}
