<?php
namespace App\Services\Pos;

use Illuminate\Http\Request;
use App\Models\POS\ProductVariant;
use App\Models\POS\Product;
use App\Pos\Variant\VariantStoreValidation;
class ProductVariantService{
    public $variant;
    public function __construct()
    {
        return $this->variant=new VariantStoreValidation();
    }
    public function index()
    {
        $variants=ProductVariant::with('product')->get();
        return view('admin.pos.variant.listing', compact('variants'));
    }

    public function add()
    {
        $products=Product::all();
        return view('admin.pos.variant.add', compact('products'));
    }

    public function store(Request $request)
    {
        try{
            $inputs=$request->all();
            $errorMessage=$this->variant->validate($inputs);
            if($errorMessage)
            {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $product=new ProductVariant();
            $product->product_id=$request->product_id;
            $product->name=$request->name;
            $product->sku=$request->sku;
            $product->barcode=$request->barcode;
            $product->price=$request->price;
            $product->cost=$request->cost;
            $product->save();
            if($product){
                return redirect()->to('admin-panel/variants/listing')->with('success', 'Record Added Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->bacK()->with('failed', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $products=Product::all();
        $product=ProductVariant::with('product')->find($id);
        if($product){
            return view('admin.pos.variant.update', compact('product', 'products'));
        }
    }

    public function update(Request $request, $id){
        try{
            $product=ProductVariant::find($id);
            if($product){
                $inputs=$request->all();
                $errorMessage=$this->variant->validate($inputs);
                if($errorMessage){
                    return redirect()->back()->with('failed', $errorMessage);
                }
            }

            $product->product_id=$request->product_id;
            $product->name=$request->name;
            $product->sku=$request->sku;
            $product->barcode=$request->barcode;
            $product->price=$request->price;
            $product->cost=$request->cost;
            $product->save();
            if($product)
            {
                return redirect()->to('admin-panel/variants/listing')->with('success', 'Record Updated Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $product=ProductVariant::find($id);
        if($product)
        {
            $product->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->back()->with('failed', 'Record Not Found');
    }
}
