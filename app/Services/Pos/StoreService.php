<?php
namespace App\Services\Pos;
use Illuminate\Http\Request;
use App\Models\POS\Store;
use App\Pos\Store\StoreValidation;
class StoreService{

    public $store;
    public function __construct()
    {
        $this->store=new StoreValidation();
    }
    public function index()
    {
        $stores=Store::all();
        return view('admin.pos.store.listing', compact('stores'));
    }

    public function add()
    {
        return view('admin.pos.store.add');
    }

    public function store(Request $request)
    {
        try{
            $inputs=$request->all();
            $errorMessage=$this->store->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }

            $store=new Store();
            $store->name=$request->name;
            $store->code=$request->code;
            $store->save();
            if($store)
            {
                return redirect()->to('admin-panel/stores/listing')->with('success', 'Store Added SuccessFully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }

    }

    public function edit($id)
    {
        $store=Store::find($id);
        if($store)
        {
            return view('admin.pos.store.update', compact('store'));
        }

        return redirect()->back()->with('failed', 'Record Not Found');
    }

    public function update(Request $request,$id)
    {
        $store=Store::find($id);
        if($store)
        {
            $inputs=$request->all();
            $errorMessage=$this->store->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }

            $store->name=$request->name;
            $store->code=$request->code;
            $store->save();
            if($store){
                return redirect()->to('admin-panel/stores/listing')->with('success', 'Store Updated Successfully');
            }
        }else{
            return redirect()->back()->with('failed', 'Record Not Found');
        }
    }

    public function destroy($id)
    {
        $store=Store::find($id);
        if($store){
            $store->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->back()->with('failed', 'Record Not Found');
    }
}
