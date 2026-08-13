<?php
namespace App\Services\Pos;
use App\Models\Pos\Supplier;
use Illuminate\Http\Request;
use App\Pos\Supplier\SupplierStoreValidation;
use App\Pos\Supplier\SupplierUpdate;
class SupplierService{

    public $supplier;
    public $sUpdate;
    public function __construct()
    {
        $this->supplier=new SupplierStoreValidation();
        $this->sUpdate=new SupplierUpdate();
    }
    public function index()
    {
        $suppliers = Supplier::where('company_id', auth()->user()?->company_id)->get();
        return view('admin.pos.supplier.listing', compact('suppliers'));
    }

    public function add()
    {
        return view('admin.pos.supplier.add');
    }

   public function store(Request $request)
{
    try {
        $inputs = $request->all();
        $errorMessage = $this->supplier->validate($inputs);

        if($errorMessage !== true) {
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }
        $supplier = new Supplier();
        $supplier->company_id = (auth()->user()?->isSuperAdmin() && $request->company_id) ? $request->company_id : auth()->user()?->company_id;
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;

        $supplier->save();

        return redirect()->to('admin-panel/supplier/listing')->with('success', 'Supplier added successfully');

    } catch (\Exception $e) {
        return redirect()->back()->with('failed', $e->getMessage());
    }
}

    public function edit(Request $request, $id)
    {
        $supplier = Supplier::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if($supplier)
        {
            return view('admin.pos.supplier.update', compact('supplier'));
        }else{
            return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
        }
    }

    public function update(Request $request,$id)
    {
        try {
        $inputs = $request->all();
        $errorMessage = $this->sUpdate->validate($inputs,$id);

        if($errorMessage !== true) {
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }
        $supplier = Supplier::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if (!$supplier) {
            return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
        }
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;

        $supplier->save();

        return redirect()->to('admin-panel/supplier/listing')->with('success', 'Supplier Updated successfully');

    } catch (\Exception $e) {
        return redirect()->back()->with('failed', $e->getMessage());
    }
}

    public function destroy($id)
    {
        $supplier = Supplier::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if($supplier)
        {
            $supplier->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }else{
            return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
        }
    }
}
