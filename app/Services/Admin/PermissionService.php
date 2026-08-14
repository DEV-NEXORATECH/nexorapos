<?php
namespace App\Services\Admin;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Validations\Admin\Permissions\PermissionStoreValidation;
use App\Validations\Admin\Permissions\PermissionUpdateValidation;
class PermissionService{
    public $permissionStoreValidation;
    public $permissionUpdateValidation;
    public function __construct(){
        $this->permissionStoreValidation=new PermissionStoreValidation();
        $this->permissionUpdateValidation=new PermissionUpdateValidation();
    }
    public function index()
    {

        if (request()->ajax()) {
            $data = Permission::with('permission_category');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('id', function($row) { return $row->id; })
                ->addColumn('permission_category', function($row) { return $row->permission_category; })
                ->addColumn('name', function($row) { return $row->name; })
                ->addColumn('route', function($row) { return $row->route; })
                ->addColumn('is_show', function($row) { return $row->is_show; })
                ->addColumn('company', function($row) { return $row->company ? $row->company->name : "-"; })
                ->addColumn('created_by', function($row) { return "-"; })
                ->addColumn('created_at', function($row) { return $row->created_at ? $row->created_at->format("d M Y H:i") : "-"; })
                ->addColumn('action', function($row) { return '<a href="'.url('admin-panel/permissions/edit/'.$row->id).'" class="nexora-action-btn is-warning"><i class="fas fa-edit"></i></a><a href="'.url('admin-panel/permissions/delete/'.$row->id).'" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Delete?\')"><i class="fas fa-trash"></i></a>'; })
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                ->rawColumns(['action'])
                ->make(true);
        }
        $permissions=Permission::with('permission_category')->get();
        return view('admin.permissions.listing', compact('permissions'));
    }

    public function add(){
        $permissions=PermissionCategory::all();
        return view('admin.permissions.add', compact('permissions'));
    }

    public function store(Request $request){
        try{
            $inputs=$request->all();
            $errorMessage=$this->permissionStoreValidation->validate($inputs);
            if($errorMessage)
            {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $permissions=new Permission();
            $permissions->permission_category_id=$request->permission_category_id;
            $permissions->name=$request->name;
            $permissions->route=$request->route;
            if($request->is_show)
            {
                $permissions->is_show='1';
            }
            $permissions->save();
            if($permissions){
                return redirect()->to('admin-panel/permissions/listing')->with('success','Permission Added Successfully');
            }else{
                return redirect()->back()->with('failed', 'Something Went Wrong');
            }
        }catch(\Exception $e){
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
//fetch permission
    public function edit(Request $request, $id)
    {
         $categories=PermissionCategory::all();
         $permissions=Permission::with('permission_category')->find($id);
        return view('admin.permissions.update', compact('permissions','categories'));
    }
//update permission

    public function update(Request $request, $id){
          try{

            $permissions=Permission::find($id);
            $inputs=$request->all();
            $errorMessage=$this->permissionUpdateValidation->validate($inputs,$id);
            if($errorMessage)
            {
                return redirect()->back()->with('failed', $errorMessage);
            }
            $permissions->permission_category_id=$request->permission_category_id;
            $permissions->name=$request->name;
            $permissions->route=$request->route;
             if($request->is_show)
            {
                $permissions->is_show='1';
            }else{
                $permissions->is_show='0';
            }
            $permissions->save();
            if($permissions){
                return redirect()->to('admin-panel/permissions/listing')->with('success','Permission Updated Successfully');
            }else{
                return redirect()->back()->with('failed', 'Something Went Wrong');
            }
        }catch(\Exception $e){
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    //delete permission

    public function destroy(Request $request,$id)
    {
        try{
            $permissions=Permission::find($id);
            $permissions->delete();
            if($permissions){
                return redirect()->to('admin-panel/permissions/listing')->with('success', 'Permission Deleted Successfully');
            }else{
                return redirect()->back()->with('failed', 'perission Deleted Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', 'Something Went wrong');
        }
    }
}
