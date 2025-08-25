<?php
namespace App\Services\Admin;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\RoleUser;
use App\Validations\Admin\RU\RoleUserStore;
class RoleUserService{

    public $roleUserStore;
    public function __construct(){
        $this->roleUserStore= new RoleUserStore();
    }


    public function index()
   {
    $users=User::with('roles')->get();
    return view('admin.RU.listing', compact('users'));
   }

   public function add()
   {
    $users=User::all();
    $roles=Role::all();
    return view('admin.RU.add', compact('users', 'roles'));
   }

   public function store(Request $request)
   {
    try{
         $inputs=$request->all();
        $errorMessage=$this->roleUserStore->validate($inputs);

        if($errorMessage)
        {
            return redirect()->back()->with('failed', $errorMessage);
        }

        $user=User::find($request->user_id);
        $user->roles()->sync($request->role_id);
        if($user)
        {
            return redirect()->to('admin-panel/user-role/listing')->with('success', 'Role is Assigned Successfully');
        }
    }catch(\Exception $e)
    {
        return redirect()->back()->with('failed', $e->getMessage());
    }


   }


   public function edit(Request $request, $id)
   {
    $users=User::all();
    $roles=Role::all();
    $user=User::with('roles')->findOrFail($id);
    return view('admin.RU.update', compact('user', 'users', 'roles'));
   }

   public function update(Request $request, $id)
   {
    try{
        $inputs=$request->all();
        $errorMessage=$this->roleUserStore->validate($inputs);
        if($errorMessage)
        {
            return redirect()->back()->with('failed', $errorMessage);
        }

        $user=User::find($request->user_id);
        $user->roles()->sync($request->role_id);

        if($user)
        {
            return redirect()->to('admin-panel/user-role/listing')->with('success', "Role Updated successfully");
        }
    }catch(\Exception $e)
    {
        return redirect()->back()->with('failed', $e->getMessage());
    }
   }

   public function destroy(Request $request, $id)
   {
        try{
            $user=User::with('roles')->findOrFail($id);
             $user->roles()->detach();
            if($user)
            {
               return redirect()->to('admin-panel/user-role/listing')->with('success', 'Role Deleted Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
   }
}
