<?php
namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Validations\Admin\User\UserStoreValidation;
use App\Validations\Admin\User\UserUpdateValidation;
use Hash;

class UserService
{
    public $userStoreValidation;

    public function __construct()
    {
        $this->userStoreValidation = new UserStoreValidation();
        $this->userUpdateValidation = new UserUpdateValidation();
    }

    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.users.listing', compact('users'));
    }

    public function post()
    {
        return view('admin.users.add');
    }

    public function store(Request $request)
    {
        try {
            $inputs = $request->all();
            $errorMessage = $this->userStoreValidation->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $users = new User();
            $users->name = $request->name;
            $users->email = $request->email;
            $users->password = Hash::make($request->password);
            $image=$request->file('image');

            if($image){
                $imageName = time(). '.'. $image->getClientOriginalExtension();
                $image->move(public_path('admin/profile_images'),$imageName);
                $users->profile_image=$image;
            }
            $saved = $users->save();

            if ($saved) {
                // ✅ Attach the selected role to the user
                $users->roles()->attach($request->role_id);

                return redirect()->to('admin-panel/users/listing')->with('success', 'User Registered Successfully');
            } else {
                return redirect()->back()->with('failed', 'Something went wrong');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        $users = User::with('roles')->find($id);
        $roles = Role::all();
        if ($users) {
            return view('admin.users.update', compact('users', 'roles'));
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $inputs = $request->all();
            $errorMessage = $this->userUpdateValidation->validate($inputs, $id);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $user = User::with('roles')->find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            if($request->hasfile('image')){
                $file=$request->file('image');
                $extension=$file->getClientOriginalExtension();
                $filename=time().'.'.$extension;
                $file->move('admin/profile_images/',$filename);
                $user->profile_image=$filename;
            }
            $user->save();

            // ✅ Sync updated role
            $user->roles()->sync([$request->role_id]);

            if ($user) {
                return redirect()->to('admin-panel/users/listing')->with('success', 'User Updated Successfully');
            } else {
                return redirect()->back()->with('failed', 'Something Went Wrong');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            if ($user) {
                return redirect()->to('admin-panel/users/listing')->with('success', 'User Deleted Successfully');
            } else {
                return redirect()->back()->with('failed', 'Something Went Wrong');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
