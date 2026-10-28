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
        if (request()->ajax()) {
            $data = ProductVariant::with(['product', 'company'])
                ->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id));

            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('id', function($row) { 
                    return '<span class="badge badge-light" style="font-weight: 700; color: #64748B;">#' . $row->id . '</span>'; 
                })
                ->addColumn('product_name', function($row) { 
                    return '<span style="font-weight: 700; color: #0058A6;">' . e(optional($row->product)->name ?? '-') . '</span>'; 
                })
                ->addColumn('name', function($row) { 
                    return '<span style="font-weight: 600; color: #0F1F3D;">' . e($row->name) . '</span>'; 
                })
                ->addColumn('sku', function($row) { 
                    return e($row->sku ?? '-'); 
                })
                ->addColumn('barcode', function($row) { 
                    return e($row->barcode ?? '-'); 
                })
                ->addColumn('price', function($row) { 
                    return '<span style="font-weight: 700; color: #10B981;">Rp ' . number_format($row->price ?? 0, 0, ',', '.') . '</span>'; 
                })
                ->addColumn('cost', function($row) { 
                    return 'Rp ' . number_format($row->cost ?? 0, 0, ',', '.') . ''; 
                })
                ->addColumn('company', function($row) { 
                    return $row->company ? e($row->company->name) : 'System'; 
                })
                ->addColumn('created_by', function($row) { 
                    return '-'; 
                })
                ->addColumn('created_at', function($row) { 
                    return $row->created_at ? $row->created_at->format('d/m/Y H:i') : '-'; 
                })
                ->addColumn('action', function($row) { 
                    return '<div class="nexora-action-group justify-content-center text-nowrap">
                        <a href="'.url('admin-panel/variants/edit/'.$row->id).'" class="nexora-action-btn is-warning" title="Edit Varian"><i class="fas fa-edit"></i></a>
                        <a href="'.url('admin-panel/variants/delete/'.$row->id).'" class="nexora-action-btn is-danger delete-category" title="Hapus Varian"><i class="fas fa-trash"></i></a>
                    </div>'; 
                })
                ->rawColumns(['id', 'product_name', 'name', 'price', 'cost', 'action'])
                ->make(true);
        }
        return view('admin.pos.variant.listing');
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
        $product = ProductVariant::when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', $companyId))->find($id);
        if($product)
        {
            $product->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }
}
