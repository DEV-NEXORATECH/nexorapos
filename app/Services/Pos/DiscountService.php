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
        if (request()->ajax()) {
            $data = Discount::with(['company']);
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('id', function($row) { return $row->id; })
                ->addColumn('name', function($row) { return $row->name; })
                ->addColumn('discount_type', function($row) { return $row->discount_type; })
                ->addColumn('discount_value', function($row) { return $row->discount_value; })
                ->addColumn('company', function($row) { return $row->company ? $row->company->name : "-"; })
                ->addColumn('created_by', function($row) { return "-"; })
                ->addColumn('created_at', function($row) { return $row->created_at ? $row->created_at->format("d M Y H:i") : "-"; })
                ->addColumn('action', function($row) { return '<a href="'.url('admin-panel/discount/edit/'.$row->id).'" class="nexora-action-btn is-warning"><i class="fas fa-edit"></i></a><a href="'.url('admin-panel/discount/delete/'.$row->id).'" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Delete?\')"><i class="fas fa-trash"></i></a>'; })
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pos.discount.listing');
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
            $discount->company_id = (auth()->user()?->isSuperAdmin() && $request->company_id) ? $request->company_id : auth()->user()?->company_id;
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
        $discount = Discount::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if($discount){
            return view('admin.pos.discount.update', compact('discount'));
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    public function update(Request $request,$id){
        try{
            $discount = Discount::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
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
        $discount = Discount::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if($discount){
            $discount->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }
}
