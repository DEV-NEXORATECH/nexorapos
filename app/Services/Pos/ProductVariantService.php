<?php
namespace App\Services\Pos;

use Illuminate\Http\Request;
use App\Models\Pos\ProductVariant;
use App\Models\Pos\Product;
use App\Pos\Variant\VariantStoreValidation;
class ProductVariantService{
    public $variant;
    public function __construct()
    {
        return $this->variant=new VariantStoreValidation();
    }
    public function index()
    {
        $companyId = auth()->user()?->company_id;
        $variants = ProductVariant::with('product')
            ->where('company_id', $companyId)
            ->get();
        return view('admin.pos.variant.listing', compact('variants'));
    }

    public function add()
    {
        $companyId = auth()->user()?->company_id;
        $products = Product::where('company_id', $companyId)->get();
        return view('admin.pos.variant.add', compact('products'));
    }

    public function store(Request $request)
    {
        try{
            $companyId = auth()->user()?->company_id;
            $inputs=$request->all();
            $errorMessage=$this->variant->validate($inputs);
            if($errorMessage)
            {
                return redirect()->back()->with('failed', $errorMessage);
            }

            if (! Product::whereKey($request->product_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Product tidak valid untuk company ini.')->withInput();
            }

            $product=new ProductVariant();
            $product->company_id = $companyId;
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
        $companyId = auth()->user()?->company_id;
        $products = Product::where('company_id', $companyId)->get();
        $product = ProductVariant::with('product')
            ->where('company_id', $companyId)
            ->find($id);
        if($product){
            return view('admin.pos.variant.update', compact('product', 'products'));
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    public function update(Request $request, $id){
        try{
            $companyId = auth()->user()?->company_id;
            $product = ProductVariant::where('company_id', $companyId)->find($id);
            if($product){
                $inputs=$request->all();
                $errorMessage=$this->variant->validate($inputs);
                if($errorMessage){
                    return redirect()->back()->with('failed', $errorMessage);
                }
            } else {
                return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
            }

            if (! Product::whereKey($request->product_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Product tidak valid untuk company ini.')->withInput();
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
        $companyId = auth()->user()?->company_id;
        $product = ProductVariant::where('company_id', $companyId)->find($id);
        if($product)
        {
            $product->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }
}
