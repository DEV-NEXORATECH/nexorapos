<?php
namespace App\Services\Pos;
use Illuminate\Http\Request;
use App\Models\POS\Store;
use App\Models\POS\Product;
use App\Models\POS\ProductVariant;
use App\Models\POS\StockMovement;
use App\Pos\Stock\StockStoreValidation;
class StockService{
    public $stock;
    public function __construct()
    {
        return $this->stock=new StockStoreValidation();
    }
    public function index()
    {
        $stocks=StockMovement::with('store', 'product', 'product_variant')->get();
        return view('admin.pos.stock.listing', compact('stocks'));
    }

    public function add()
    {
        $stores=Store::all();
        $products=Product::all();
        $variants=ProductVariant::all();
        return view('admin.pos.stock.add', compact('stores', 'products', 'variants'));
    }

    public function store(Request $request)
    {
        try{
            $inputs=$request->all();
            $errorMessage=$this->stock->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }

            $stock=new StockMovement();
            $stock->store_id=$request->store_id;
            $stock->product_id=$request->product_id;
            $stock->product_variant_id=$request->product_variant_id;
            $stock->quantity=$request->quantity;
            $stock->type=$request->type;
            $stock->save();
            if($stock)
            {
                return redirect()->to('admin-panel/stock/listing')->with('success', 'Record Added Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $stores=Store::all();
        $products=Product::all();
        $variants=ProductVariant::all();
        $stock=StockMovement::with('store', 'product', 'product_variant')->find($id);
        if($stock)
        {
            return view('admin.pos.stock.update', compact('stock', 'stores', 'products', 'variants'));
        }

        return redirect()->back()->with('failed', 'Record Not Found');
    }

    public function update(Request $request, $id)
    {
        try{
            $stock=StockMovement::find($id);
            if($stock)
            {
                $inputs=$request->all();
                $errorMessage=$this->stock->validate($inputs);
                if($errorMessage){
                    return redirect()->back()->with('failed', $errorMessage);
                }
            }

            $stock->store_id=$request->store_id;
            $stock->product_id=$request->product_id;
            $stock->product_variant_id=$request->product_variant_id;
            $stock->quantity=$request->quantity;
            $stock->type=$request->type;
            $stock->save();
            if($stock){
                return redirect()->to('admin-panel/stock/listing')->with('success', 'Record Added Successfully');
            }
        }catch(\Exception $e){
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $stock=StockMovement::find($id);
        if($stock){
            $stock->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->back()->with('failed', 'Record Not Found');
    }
}
