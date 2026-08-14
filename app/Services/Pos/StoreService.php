<?php
namespace App\Services\Pos;
use Illuminate\Http\Request;
use App\Models\Pos\Store;
use App\Pos\Store\StoreValidation;
class StoreService{

    public $store;
    public function __construct()
    {
        $this->store=new StoreValidation();
    }
    public function index()
    {
        if (request()->ajax()) {
            $stores = Store::with(["company"]);
            return \Yajra\DataTables\Facades\DataTables::of($stores)
                ->addColumn('code', function ($row) {
                    return '<strong>' . ($row->code ?? '-') . '</strong>';
                })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('created_at_short', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y') : '-';
                })
                ->addColumn('company', function ($row) {
                    return $row->company ? $row->company->name : 'System';
                })
                ->addColumn('created_by', function ($row) {
                    return '-';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a href="'.url('admin-panel/stores/show/'.$row->id).'" class="nexora-action-btn is-info" title="View"><i class="fas fa-eye"></i></a>
                    <a href="'.url('admin-panel/stores/edit/'.$row->id).'" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                    <a href="'.url('admin-panel/stores/delete/'.$row->id).'" class="nexora-action-btn is-danger" onclick="return confirm(\'Delete?\')" title="Delete"><i class="fas fa-trash"></i></a>';
                })
                ->rawColumns(['code', 'action'])
                ->make(true);
        }
        return view('admin.pos.store.listing');
    }

    public function add()
    {
        return view('admin.pos.store.add');
    }

    public function show($id)
    {
        $store = Store::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if($store) {
            return view('admin.pos.store.show', compact('store'));
        }
        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
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
            $store->company_id=(auth()->user()?->isSuperAdmin() && $request->company_id) ? $request->company_id : auth()->user()?->company_id;
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
        $store=Store::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if($store)
        {
            return view('admin.pos.store.update', compact('store'));
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    public function update(Request $request,$id)
    {
        $store=Store::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
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
            return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
        }
    }

    public function destroy($id)
    {
        $store=Store::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if($store){
            $store->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }
}
