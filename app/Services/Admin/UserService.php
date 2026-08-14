<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use App\Support\AuditLogger;
use App\Validations\Admin\User\UserStoreValidation;
use App\Validations\Admin\User\UserUpdateValidation;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
        $currentUser = auth()->user();
        if (request()->ajax()) {
            $users = User::with(['role', 'company'])
                ->when(! $currentUser?->isSuperAdmin(), fn ($query) => $query->where('company_id', $currentUser?->company_id));
            
            return \Yajra\DataTables\Facades\DataTables::of($users)
                ->addColumn('nama', function ($row) { return $row->name; })
                ->addColumn('email', function ($row) { return $row->email; })
                ->addColumn('role', function ($row) { return $row->role ? $row->role->role : '-'; })
                ->addColumn('company', function ($row) { return $row->company ? $row->company->name : '-'; })
                ->addColumn('license', function ($row) { return $row->company ? $row->company->subscription_status : '-'; })
                ->addColumn('status', function ($row) { return $row->company && $row->company->is_active ? 'Active' : 'Inactive'; })
                ->addColumn('action', function ($row) {
                    return '<a href="'.url('admin-panel/users/edit/'.$row->id).'" class="nexora-action-btn is-warning"><i class="fas fa-edit"></i></a>
                            <a href="'.url('admin-panel/users/delete/'.$row->id).'" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Delete?\')"><i class="fas fa-trash"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.users.listing');
    }

    // Show add user form
    public function post()
    {
        $currentUser = auth()->user();
        $roles = $currentUser?->isSuperAdmin()
            ? Role::withoutGlobalScopes()->orderBy('role')->get()
            : Role::all();
        $companies = $currentUser?->isSuperAdmin()
            ? Company::orderBy('name')->get()
            : collect();

        return view('admin.users.add', compact('roles', 'companies'));
    }

    // Store new user
    public function store(Request $request)
    {
        try {
            $currentUser = auth()->user();
            if (! $currentUser?->isSuperAdmin() && $request->boolean('is_super_admin')) {
                abort(403, 'Admin company tidak boleh membuat Super Admin.');
            }

            $inputs = $request->all();
            $errorMessage = $this->userStoreValidation->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $companyId = $currentUser?->isSuperAdmin()
                ? $request->company_id
                : $currentUser?->company_id;
            $company = Company::findOrFail($companyId);

            if (! $currentUser?->isSuperAdmin() && $company->userLimitReached()) {
                return redirect()->back()->with('failed', 'User limit has been reached.')->withInput();
            }

            $role = Role::findOrFail($request->role_id);
            if (! $currentUser?->isSuperAdmin() && (int) $role->company_id !== (int) $currentUser->company_id) {
                abort(403, 'Role tidak tersedia untuk company ini.');
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->role_id = $role->id;
            $user->company_id = $company->id;
            $user->is_super_admin = $currentUser?->isSuperAdmin() && $request->boolean('is_super_admin');

            // Handle profile image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('admin/profile_images'), $imageName);
                $user->profile_image = $imageName;
            }

            $saved = $user->save();

            if ($saved) {
                AuditLogger::log('create', 'users', $user, [], $user->only(['id', 'name', 'email', 'company_id', 'role_id', 'is_super_admin']));
                return redirect()->to('admin-panel/users/listing')->with('success', 'User Registered Successfully');
            } else {
                return redirect()->back()->with('failed', 'Something went wrong');
            }
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // Show edit user form
    public function edit(Request $request, $id)
    {
        $currentUser = auth()->user();
        $user = User::with('role')->findOrFail($id);
        if (! $currentUser?->isSuperAdmin() && (int) $user->company_id !== (int) $currentUser?->company_id) {
            abort(403, 'User bukan milik company kamu.');
        }
        $roles = $currentUser?->isSuperAdmin()
            ? Role::withoutGlobalScopes()->orderBy('role')->get()
            : Role::all();
        $companies = $currentUser?->isSuperAdmin()
            ? Company::orderBy('name')->get()
            : collect();
        if ($user) {
            return view('admin.users.update', compact('user', 'roles', 'companies'));
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

            $currentUser = auth()->user();
            $user = User::with('role')->findOrFail($id);
            if (! $currentUser?->isSuperAdmin() && (int) $user->company_id !== (int) $currentUser?->company_id) {
                abort(403, 'User bukan milik company kamu.');
            }
            if (! $currentUser?->isSuperAdmin() && ($request->boolean('is_super_admin') || $user->isSuperAdmin())) {
                abort(403, 'Admin company tidak boleh mengubah privilege Super Admin.');
            }
            $role = Role::findOrFail($request->role_id);
            if (! $currentUser?->isSuperAdmin() && (int) $role->company_id !== (int) $currentUser?->company_id) {
                abort(403, 'Role tidak tersedia untuk company ini.');
            }
            $oldValue = $user->only(['name', 'email', 'phone', 'role_id', 'is_super_admin']);
            $companyId = $currentUser?->isSuperAdmin()
                ? $request->input('company_id', $user->company_id)
                : $user->company_id;
            $company = Company::findOrFail($companyId);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role_id = $role->id;
            $user->company_id = $company->id;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Handle profile image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('admin/profile_images'), $filename);
                $user->profile_image = $filename;
            }

            $user->save();
            AuditLogger::log('update', 'users', $user, $oldValue, $user->only(['name', 'email', 'phone', 'role_id', 'is_super_admin']));

            return redirect()->to('admin-panel/users/listing')->with('success', 'User Updated Successfully');
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // Delete user
    public function destroy(Request $request, $id)
    {
        try {
            $currentUser = auth()->user();
            $user = User::findOrFail($id);
            if (! $currentUser?->isSuperAdmin() && (int) $user->company_id !== (int) $currentUser?->company_id) {
                abort(403, 'User bukan milik company kamu.');
            }
            if (! $currentUser?->isSuperAdmin() && $user->isSuperAdmin()) {
                abort(403, 'Admin company tidak boleh menghapus Super Admin.');
            }
            if ($user) {
                $oldValue = $user->only(['id', 'name', 'email', 'company_id', 'role_id', 'is_super_admin']);
                $user->delete();
                AuditLogger::log('delete', 'users', $user, $oldValue, []);
                return redirect()->to('admin-panel/users/listing')->with('success', 'User Deleted Successfully');
            } else {
                return redirect()->back()->with('failed', 'User not found');
            }
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
