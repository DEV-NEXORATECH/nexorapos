<?php
namespace App\Services\Pos;
use App\Models\Pos\Tax;
use Illuminate\Http\Request;
use App\Pos\Tax\TaxStoreValidation;

class TaxService {
    public function index() {
        $taxes = Tax::where('company_id', auth()->user()?->company_id)->get();
        return view('admin.pos.tax.listing', compact('taxes'));
    }

    public function add() {
        return view('admin.pos.tax.add');
    }

    public function store(Request $request) {
        try{
            $inputs = $request->all();
            $validation = new TaxStoreValidation();
            $errors = $validation->validate($inputs);
            if ($errors) {
                return redirect()->back()->with('error', $errors)->withInput();
            }
            $tax = new Tax();
            $tax->company_id = auth()->user()?->company_id;
            $tax->name = $request->name;
            $tax->rate = $request->rate;
            $tax->save();
            return redirect('admin-panel/tax/listing')->with('success', 'Tax added successfully.');

        }catch(\Exception $e)
            {
                return redirect()->back()->with('failed', $e->getMessage())->withInput();
            }
    }

    // Edit Tax
    public function edit(Request $request, $id) {
            $tax = Tax::where('id', $id)->where('company_id', auth()->user()?->company_id)->first();
            if (! $tax) {
                return redirect('admin-panel/tax/listing')->with('failed', 'Tax Not Found');
            }
            return view('admin.pos.tax.update', compact('tax'));
}

//update tax
    public function update(Request $request, $id) {
        try{
            $inputs=$request->all();
            $validation=new TaxStoreValidation();
            $errors=$validation->validate($inputs,$id);
            if($errors)
            {
                return redirect()->back()->with('error',$errors)->withInput();
            }
            $tax = Tax::where('id', $id)->where('company_id', auth()->user()?->company_id)->first();
            if (!$tax) {
                return redirect('admin-panel/tax/listing')->with('failed', 'Tax Not Found');
            }
            $tax->name=$request->name;
            $tax->rate=$request->rate;
            $tax->save();
            return redirect('admin-panel/tax/listing')->with('success','Tax updated successfully.');
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage())->withInput();
        }
}

//destory tax

    public function destroy(Request $request,$id)
    {
        try{
            $tax = Tax::where('id', $id)->where('company_id', auth()->user()?->company_id)->first();
            if($tax)
            {
                $tax->delete();
                return redirect()->to('admin-panel/tax/listing')->with('success', 'Tax Deleted Successfully');
            }
            return redirect()->back()->with('failed', 'Tax Not Found');
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
