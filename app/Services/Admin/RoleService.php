<?php
namespace App\Services\Admin;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\RolePermission;
use App\Validations\Admin\Roles\RoleStoreValidation;
use App\Validations\Admin\Roles\RoleUpdateValidation;
class RoleService{

    public $roleStoreValidation;
    public $roleUpdateValidation;
    public function __construct(){
        $this->roleStoreValidation=new RoleStoreValidation();
        $this->roleUpdateValidation=new RoleUpdateValidation();
    }

    public function index()
    {
        $roles=Role::all();
        return view('admin.roles.listing',compact('roles'));
    }

    public function add()
    {
        return view('admin.roles.add');
    }

    public function store(Request $request){
        try{
            $inputs=$request->all();
            $errorMessage=$this->roleStoreValidation->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }

            $roles=new Role();
            $roles->role=$request->role;
            $roles->save();
            if($roles){
                return redirect()->to('admin-panel/roles/listing')->with('success', 'Role Added Successfully');
            }else{
                return redirect()->back()->with('failed', 'Something went wrong');
            }
        }catch(\Exception $e){
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
{
    $permissionCategories = PermissionCategory::with('permissions')->orderBy('display_order', 'asc')->get();
    try {
        $roles = Role::find($id);
        if ($roles) {
            // ✅ Get assigned permissions of the role
          $assignedPermissions = $roles->permissions()->pluck('permissions.id')->toArray();
            // ✅ Pass it to the view
            return view('admin.roles.update', compact('roles', 'permissionCategories', 'assignedPermissions'));
        } else {
            return redirect()->back()->with('failed', 'Data Not Found');
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('failed', $e->getMessage());
    }
}


    //update function


        public function update(Request $request, $id)
        {
            try {
                // 1. VALIDATION
                $inputs = $request->all();
                $errorMessage = $this->roleUpdateValidation->validate($inputs);

                if ($errorMessage) {
                    return redirect()->back()->with('failed', $errorMessage);
                }

                // 2. FIND THE ROLE
                $role = Role::findOrFail($id);

                // 3. UPDATE ROLE NAME
                $role->role = $request->role;
                $role->save(); // Save the name change first

                // 4. SYNC PERMISSIONS (PIVOT TABLE)
                if ($request->has('permissions')) {
                    $role->permissions()->sync($request->permissions);
                } else {
                    // If no permissions selected, detach all
                    $role->permissions()->detach();
                }

                // 5. RETURN RESPONSE
                return redirect()->to('admin-panel/roles/listing')
                    ->with('success', 'Role and permissions updated successfully');

            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('failed', 'Error: ' . $e->getMessage());
            }
        }

        //delete function

    public function destroy(Request $request,$id){
        try{
            $roles=Role::find($id);
            $roles->delete();
            if($roles){
                return redirect()->to('admin-panel/roles/listing')->with('success', 'Role Deleted Successfully');
            }else{
                return redirect()->back()->with('failed', 'Something Went Wrong');
            }
        }catch(\Exception $e){
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
