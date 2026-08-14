<?php

namespace App\Services\Admin;

use App\Models\PermissionCategory;
use App\Models\Role;
use App\Models\Permission;
use App\Validations\Admin\Roles\RoleStoreValidation;
use App\Validations\Admin\Roles\RoleUpdateValidation;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class RoleService
{
    public RoleStoreValidation $roleStoreValidation;
    public RoleUpdateValidation $roleUpdateValidation;

    public function __construct()
    {
        $this->roleStoreValidation = new RoleStoreValidation();
        $this->roleUpdateValidation = new RoleUpdateValidation();
    }

    public function index()
    {
        if (request()->ajax()) {
            $data = Role::with(["company"]);
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('role', function($row) { return $row->role ?? $row->name; })
                ->addColumn('users', function($row) { return $row->users ? $row->users->count() : 0; })
                ->addColumn('access', function($row) { return "Full Access"; })
                ->addColumn('status', function($row) { return "Active"; })
                ->addColumn('company', function($row) { return $row->company ? $row->company->name : "-"; })
                ->addColumn('created_by', function($row) { return "-"; })
                ->addColumn('created_at', function($row) { return $row->created_at ? $row->created_at->format("d M Y H:i") : "-"; })
                ->addColumn('action', function($row) { return '<a href="'.url('admin-panel/roles/edit/'.$row->id).'" class="nexora-action-btn is-warning"><i class="fas fa-edit"></i></a><a href="'.url('admin-panel/roles/delete/'.$row->id).'" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Delete?\')"><i class="fas fa-trash"></i></a>'; })
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.roles.listing');
    }

    public function add()
    {
        $permissionCategories = PermissionCategory::with('permissions')
            ->orderBy('display_order', 'asc')
            ->get();

        return view('admin.roles.add', compact('permissionCategories'));
    }

    public function store(Request $request)
    {
        $errorMessage = $this->roleStoreValidation->validate($request->all());
        if ($errorMessage) {
            return redirect()->back()->with('failed', $errorMessage);
        }

        $currentUser = auth()->user();
        $requestedPermissions = collect($request->input('permissions', []))
            ->map(fn ($permissionId) => (int) $permissionId)
            ->all();

        if (! $currentUser?->isSuperAdmin()) {
            $allowedPermissionIds = $currentUser->role?->permissions()
                ->pluck('permissions.id')
                ->map(fn ($permissionId) => (int) $permissionId)
                ->all() ?? [];

            if (array_diff($requestedPermissions, $allowedPermissionIds) !== []) {
                abort(403, 'Admin tidak boleh memberikan permission yang tidak dimiliki.');
            }

            $disabledFeaturePermissions = Permission::whereIn('id', $requestedPermissions)
                ->get()
                ->filter(function ($permission) use ($currentUser) {
                    $featureKey = $permission->permission_category?->feature_key;

                    return $featureKey && ! $currentUser->company?->hasFeature($featureKey);
                });

            if ($disabledFeaturePermissions->isNotEmpty()) {
                abort(403, 'Admin tidak boleh memberikan permission dari fitur company yang nonaktif.');
            }
        }

        $role = new Role();
        $role->role = $request->role;

        if (! $currentUser?->isSuperAdmin()) {
            $role->company_id = $currentUser?->company_id;
        }

        $role->save();
        $role->permissions()->sync($requestedPermissions);
        AuditLogger::log('create', 'roles', $role, [], [
            'id' => $role->id,
            'role' => $role->role,
            'company_id' => $role->company_id,
            'permissions' => $requestedPermissions,
        ]);

        return redirect()->to('admin-panel/roles/listing')->with('success', 'Role Added Successfully');
    }

    public function edit(Request $request, $id)
    {
        $currentUser = auth()->user();
        $role = Role::findOrFail($id);

        if (! $currentUser?->isSuperAdmin() && (int) $role->company_id !== (int) $currentUser?->company_id) {
            abort(403, 'Role bukan milik company kamu.');
        }

        $permissionCategories = PermissionCategory::with('permissions')
            ->orderBy('display_order', 'asc')
            ->get();
        $assignedPermissions = $role->permissions()->pluck('permissions.id')->toArray();

        return view('admin.roles.update', [
            'roles' => $role,
            'permissionCategories' => $permissionCategories,
            'assignedPermissions' => $assignedPermissions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $errorMessage = $this->roleUpdateValidation->validate($request->all());
        if ($errorMessage) {
            return redirect()->back()->with('failed', $errorMessage);
        }

        $currentUser = auth()->user();
        $role = Role::findOrFail($id);

        if (! $currentUser?->isSuperAdmin() && (int) $role->company_id !== (int) $currentUser?->company_id) {
            abort(403, 'Role bukan milik company kamu.');
        }

        $requestedPermissions = collect($request->input('permissions', []))
            ->map(fn ($permissionId) => (int) $permissionId)
            ->all();

        if (! $currentUser?->isSuperAdmin()) {
            $allowedPermissionIds = $currentUser->role?->permissions()
                ->pluck('permissions.id')
                ->map(fn ($permissionId) => (int) $permissionId)
                ->all() ?? [];

            if (array_diff($requestedPermissions, $allowedPermissionIds) !== []) {
                abort(403, 'Admin tidak boleh memberikan permission yang tidak dimiliki.');
            }

            $disabledFeaturePermissions = Permission::whereIn('id', $requestedPermissions)
                ->get()
                ->filter(function ($permission) use ($currentUser) {
                    $featureKey = $permission->permission_category?->feature_key;

                    return $featureKey && ! $currentUser->company?->hasFeature($featureKey);
                });

            if ($disabledFeaturePermissions->isNotEmpty()) {
                abort(403, 'Admin tidak boleh memberikan permission dari fitur company yang nonaktif.');
            }
        }

        $oldValue = [
            'role' => $role->role,
            'permissions' => $role->permissions()->pluck('permissions.id')->all(),
        ];

        $role->role = $request->role;
        $role->save();
        $role->permissions()->sync($requestedPermissions);
        AuditLogger::log('update', 'roles', $role, $oldValue, [
            'role' => $role->role,
            'permissions' => $requestedPermissions,
        ]);

        return redirect()->to('admin-panel/roles/listing')
            ->with('success', 'Role and permissions updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $currentUser = auth()->user();
        $role = Role::findOrFail($id);

        if (! $currentUser?->isSuperAdmin() && (int) $role->company_id !== (int) $currentUser?->company_id) {
            abort(403, 'Role bukan milik company kamu.');
        }

        if ($role->users()->exists()) {
            return redirect()->back()->with('failed', 'Role masih digunakan user.');
        }

        $oldValue = $role->only(['id', 'role', 'company_id']);
        $role->delete();
        AuditLogger::log('delete', 'roles', $role, $oldValue, []);

        return redirect()->to('admin-panel/roles/listing')->with('success', 'Role Deleted Successfully');
    }
}
