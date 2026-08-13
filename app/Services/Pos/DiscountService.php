<?php
namespace App\Services\Pos;
use App\Models\Pos\Discount;
use Illuminate\Http\Request;
use App\Pos\Discount\DiscountStoreValidation;
class DiscountService{
    public $discount;
    public function __construct()
    {
        return $this->discount=new DiscountStoreValidation();
    }
    public function index()
    {
        $discounts = Discount::where('company_id', auth()->user()?->company_id)->get();
        return view('admin.pos.discount.listing', compact('discounts'));
    }

    public function add()
    {
        return view('admin.pos.discount.add');
    }

    public function store(Request $request)
    {
        try{
            $inputs=$request->all();
            $errorMessage=$this->discount->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }

            $discount= new Discount();
            $discount->company_id = auth()->user()?->company_id;
            $discount->name=$request->name;
            $discount->type=$request->type;
            $discount->value=$request->value;
            $discount->save();
            if($discount){
                return redirect()->to('admin-panel/discounts/listing')->with('success', 'Record Added SuccesFully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $discount = Discount::where('id', $id)->where('company_id', auth()->user()?->company_id)->first();
        if($discount){
            return view('admin.pos.discount.update', compact('discount'));
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    public function update(Request $request,$id){
        try{
            $discount = Discount::where('id', $id)->where('company_id', auth()->user()?->company_id)->first();
            if(!$discount){
                return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
            }
            $inputs=$request->all();
            $errorMessage=$this->discount->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }

            $discount->name=$request->name;
            $discount->type=$request->type;
            $discount->value=$request->value;
            $discount->save();
            if($discount){
                return redirect()->to('admin-panel/discounts/listing')->with('success', 'Record Updated Successfully');
            }
        }catch(\Exception $e){
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function destroy($id){
        $discount = Discount::where('id', $id)->where('company_id', auth()->user()?->company_id)->first();
        if($discount){
            $discount->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }
}
