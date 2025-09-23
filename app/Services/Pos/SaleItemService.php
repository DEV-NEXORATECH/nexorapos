<?php
namespace App\Services\Pos;
use Illuminate\Http\Request;
use App\Models\Pos\SaleItem;
use App\Models\Pos\Sale;
use App\Models\Pos\Product;
use App\Models\Pos\ProductVariant;
use App\Pos\SaleItem\SaleItemValidation;
class SaleItemService{
    public $sale;
    public function __construct()
    {
        return $this->sale= new SaleItemValidation();
    }
    public function index()
    {
        $sales=SaleItem::with('sale', 'product', 'product_variant')->get();
        return view('admin.pos.saleItem.listing', compact('sales'));
    }

    public function add()
    {
        $sales=Sale::all();
        $products=Product::all();
        $variants=ProductVariant::all();
        return view('admin.pos.saleItem.add', compact('sales', 'products', 'variants'));
    }

    public function store(Request $request)
    {
        try{
            $inputs=$request->all();
            $errorMessage=$this->sale->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }

            $sale=new SaleItem();
            $sale->sale_id=$request->sale_id;
            $sale->product_id=$request->product_id;
            $sale->product_variant_id=$request->product_variant_id;
            $sale->quantity=$request->quantity;
            $sale->price=$request->price;
            $sale->discount=$request->discount;
            $sale->tax=$request->tax;
            $sale->save();
            if($sale)
            {
                return redirect()->to('admin-panel/sale-items/listing')->with('success', 'Record Added SuccessFully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }


    public function edit($id)
    {
        $sales=Sale::all();
        $products=Product::all();
        $variants=ProductVariant::all();
        $sale=SaleItem::find($id);
        if($sale)
        {
            return view('admin.pos.saleItem.update', compact('sale', 'sales', 'products', 'variants'));
        }

        return redirect()->back()->with('failed', 'Record Not Found');
    }

    public function update(Request $request,$id)
    {
        try{
            $sale=SaleItem::find($id);
            if($sale){
                $inputs=$request->all();
                $errorMessage=$this->sale->validate($inputs);
                if($errorMessage){
                    return redirect()->back()->with('failed', $errorMessage);
                }
            }
            $sale->sale_id=$request->sale_id;
            $sale->product_id=$request->product_id;
            $sale->product_variant_id=$request->product_variant_id;
            $sale->quantity=$request->quantity;
            $sale->price=$request->price;
            $sale->discount=$request->discount;
            $sale->tax=$request->tax;
            $sale->save();
            if($sale)
            {
                return redirect()->to('admin-panel/sale-items/listing')->with('success', 'Record Updated SuccessFully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $sale=SaleItem::find($id);
        if($sale){
            $sale->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->back()->with('failed', 'Record NOt Found');
    }
}
