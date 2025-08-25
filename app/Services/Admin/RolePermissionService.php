<?php
namespace App\Services\Admin;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Validations\Admin\RP\RolePermissionStore;
use App\Models\RolePermission;

class RolePermissionService{

    public $rolePermissionStore;

    public function __construct()
    {
        $this->rolePermissionStore=new RolePermissionStore();
    }

    public function index()
    {
        $roles=Role::with('permissions')->get();
        return view('admin.rolePermission.listing', compact('roles'));
    }

    public function add()
    {
        $roles=Role::all();
        $permissions=Permission::all();
        return view('admin.rolePermission.add',compact('roles', 'permissions'));
    }


        public function store(Request $request)

        {
            try{
                 $inputs=$request->all();

                 $errorMessage= $this->rolePermissionStore->validate($inputs);

                if($errorMessage){
                    return redirect()->back()->with('failed', $errorMessage);
                }

                $role=Role::find($request->role_id);
                $role->permissions()->sync($request->permissions);
                return redirect()->to('admin-panel/role-permissions/listing')->with('success', 'Permissions Assigned to the Roles');
            }catch(\Exception $e)
            {
                return redirect()->back()->with('failed', $e->getMessage());
            }

        }


        //function to update record
        public function edit(Request $request,$id)
        {
            $roles=Role::all();
            $role=Role::with('permissions')->findOrFail($id);
               $permissions = Permission::all();
            return view('admin.rolePermission.update', compact('role', 'permissions', 'roles'));
        }

        public function update(Request $request,$id)
        {
            try{
                $inputs=$request->all();

                 $errorMessage= $this->rolePermissionStore->validate($inputs);

                if($errorMessage){
                    return redirect()->back()->with('failed', $errorMessage);
                }
                 $role=Role::find($request->role_id);
                $role->permissions()->sync($request->permissions);
                return redirect()->to('admin-panel/role-permissions/listing')->with('success', 'Permissions Updated ');
            }catch(\Exception $e)
            {
                return redirect()->back()->with('failed', $e->getMessage());
            }


            }

        //function to delete the record

        public function destroy(Request $request,$id)
        {
            try{
                $role = Role::findOrFail($id);

                    // Detach all permissions assigned to this role
                 $role->permissions()->detach();
                if($role)
                {
                    return redirect()->to('admin-panel/role-permissions/listing')->with('success', 'Record Deleted Successfully');
                }else{
                    return redirect()->back()->with('failed', 'SomeThing Went wrong');
                }
            }catch(\Exception $e)
            {
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
}
