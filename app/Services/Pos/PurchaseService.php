<?php
namespace App\Services\Pos;
use App\Models\Pos\Purchase;
use Illuminate\Http\Request;
use App\Models\Pos\Supplier;
use App\Pos\Purchase\PurchaseStoreValidation;
class PurchaseService{
    public $pusrchase;
    public function __construct()
    {
        $this->purchase=new PurchaseStoreValidation();
    }
    public function index()
    {
        $purchases=Purchase::with('supplier')->get();
        return view('admin.pos.purchase.listing', compact('purchases'));
    }

    public function add()
    {
        $suppliers=Supplier::all();
        return view('admin.pos.purchase.add', compact('suppliers'));
    }

    public function store(Request $request)
    {
        try{
            $inputs=$request->all();
            $errorMessage=$this->purchase->validate($inputs);
            if($errorMessage)
            {
                return redirect()->back()->withErrors()->withInputs();
            }

            $purchase=new Purchase();
            $purchase->supplier_id=$request->supplier_id;
            $purchase->total_amount=$request->total_amount;
            $purchase->status=$request->status;
            $purchase->save();
            if($purchase){
                return redirect()->to('admin-panel/purchase/listing')->with('success', "Record Added Successfully");
            }
        }catch(\Exception $e){
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function edit($id)
    {
    $suppliers=Supplier::all();
    $purchase=Purchase::with('supplier')->find($id);
    if($purchase)
    {
        return view('admin.pos.purchase.update', compact('purchase', 'suppliers'));
    }else{
        return redirect()->back()->with('failed', "Record Not Found");
    }
}

    public function update(Request $request,$id)
    {
        try{
            $purchase=Purchase::find($id);
            if($purchase){
                $inputs=$request->all();
                $errorMessage=$this->purchase->validate($inputs);
                if($errorMessage){
                    return redirect()->back()->with('failed', $errorMessage);
                }

            }
            $purchase->supplier_id=$request->supplier_id;
            $purchase->status=$request->status;
            $purchase->total_amount=$request->total_amount;
            $purchase->save();
            return redirect()->to('admin-panel/purchase/listing')->with('success', 'Record Updated Successfully');
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    ///delete record function

    public function destroy($id)
    {
        $purchase=Purchase::find($id);
        if($purchase)
        {
            $purchase->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->back()->with('failed', "Record Not Found");
    }
}
