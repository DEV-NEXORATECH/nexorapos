<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Validations\Admin\User\UserStoreValidation;
use App\Validations\Admin\User\UserUpdateValidation;

class UserService
{
    public $userStoreValidation;
    public $userUpdateValidation;

    public function __construct()
    {
        $this->userStoreValidation = new UserStoreValidation();
        $this->userUpdateValidation = new UserUpdateValidation();
    }

    // List all users
    public function index()
    {
        $users = User::with('role')->get(); // role() relationship exists
        return view('admin.users.listing', compact('users'));
    }

    // Show add user form
    public function post()
    {
        $roles = Role::all();
        return view('admin.users.add', compact('roles'));
    }

    // Store new user
    public function store(Request $request)
    {
        try {
            $inputs = $request->all();
            $errorMessage = $this->userStoreValidation->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role_id = $request->role_id; // assign role directly

            // Handle profile image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('admin/profile_images'), $imageName);
                $user->profile_image = $imageName;
            }

            $saved = $user->save();

            if ($saved) {
                return redirect()->to('admin-panel/users/listing')->with('success', 'User Registered Successfully');
            } else {
                return redirect()->back()->with('failed', 'Something went wrong');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // Show edit user form
    public function edit(Request $request, $id)
    {
        $user = User::with('role')->find($id);
        $roles = Role::all();
        if ($user) {
            return view('admin.users.update', compact('user', 'roles'));
        }
        return redirect()->back()->with('failed', 'User not found');
    }

    // Update user
    public function update(Request $request, $id)
    {
        try {
            $inputs = $request->all();
            $errorMessage = $this->userUpdateValidation->validate($inputs, $id);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $user = User::with('role')->find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role_id = $request->role_id; // update role

            // Handle profile image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('admin/profile_images'), $filename);
                $user->profile_image = $filename;
            }

            $user->save();

            return redirect()->to('admin-panel/users/listing')->with('success', 'User Updated Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // Delete user
    public function destroy(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $user->delete();
                return redirect()->to('admin-panel/users/listing')->with('success', 'User Deleted Successfully');
            } else {
                return redirect()->back()->with('failed', 'User not found');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
