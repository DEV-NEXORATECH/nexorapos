<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role;
use App\Models\User;
use App\Models\Pos\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AccessController extends Controller
{
    public function users(Request $request)
    {
        $companyId = $this->companyId($request);
        $users = User::withoutGlobalScopes()
            ->with(['company', 'role', 'store'])
            ->where('company_id', $companyId)
            ->latest('id')
            ->paginate((int) $request->get('per_page', 20));

        return $this->json(true, 'User list.', ['users' => $users]);
    }

    public function showUser(Request $request, int $id)
    {
        $user = $this->userRecord($request, $id);

        return $this->json(true, 'User detail.', ['user' => $user]);
    }

    public function storeUser(Request $request)
    {
        $companyId = $this->companyId($request);
        $company = Company::find($companyId);
        if ($company?->userLimitReached()) {
            return $this->json(false, 'Limit user company sudah penuh.', ['license' => $company->licenseSnapshot()], 422);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'integer'],
            'store_id' => ['nullable', 'integer'],
            'shift_name' => ['nullable', 'string', 'max:100'],
            'shift_start' => ['nullable', 'date_format:H:i'],
            'shift_end' => ['nullable', 'date_format:H:i'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $this->assertRoleStore($data, $companyId);

        $user = User::withoutGlobalScopes()->create([
            'company_id' => $companyId,
            'is_super_admin' => false,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role_id' => $data['role_id'],
            'store_id' => $data['store_id'] ?? null,
            'shift_name' => $data['shift_name'] ?? null,
            'shift_start' => $data['shift_start'] ?? null,
            'shift_end' => $data['shift_end'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        return $this->json(true, 'User berhasil dibuat.', ['user' => $user->load(['company', 'role', 'store'])], 201);
    }

    public function updateUser(Request $request, int $id)
    {
        $user = $this->userRecord($request, $id);
        $companyId = $this->companyId($request);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['sometimes', 'integer'],
            'store_id' => ['nullable', 'integer'],
            'shift_name' => ['nullable', 'string', 'max:100'],
            'shift_start' => ['nullable', 'date_format:H:i'],
            'shift_end' => ['nullable', 'date_format:H:i'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $this->assertRoleStore($data, $companyId);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['company_id'] = $companyId;
        $data['is_super_admin'] = false;
        $user->fill($data)->save();

        return $this->json(true, 'User berhasil diupdate.', ['user' => $user->fresh(['company', 'role', 'store'])]);
    }

    public function destroyUser(Request $request, int $id)
    {
        $user = $this->userRecord($request, $id);

        if ((int) $user->id === (int) $request->user()->id) {
            return $this->json(false, 'Tidak bisa hapus akun sendiri.', null, 422);
        }

        $user->delete();

        return $this->json(true, 'User berhasil dihapus.');
    }

    public function roles(Request $request)
    {
        $companyId = $this->companyId($request);
        $roles = Role::withoutGlobalScopes()
            ->with('permissions.permission_category')
            ->where('company_id', $companyId)
            ->latest('id')
            ->paginate((int) $request->get('per_page', 20));

        return $this->json(true, 'Role list.', ['roles' => $roles]);
    }

    public function storeRole(Request $request)
    {
        $companyId = $this->companyId($request);
        $data = $request->validate([
            'role' => ['required', 'string', 'max:255'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer'],
        ]);

        $permissionIds = $this->validPermissionIds($data['permission_ids'] ?? [], $companyId);
        $role = Role::withoutGlobalScopes()->create([
            'company_id' => $companyId,
            'role' => $data['role'],
        ]);
        $role->permissions()->sync($permissionIds);

        return $this->json(true, 'Role berhasil dibuat.', ['role' => $role->load('permissions')], 201);
    }

    public function updateRole(Request $request, int $id)
    {
        $companyId = $this->companyId($request);
        $role = Role::withoutGlobalScopes()->where('company_id', $companyId)->findOrFail($id);
        $data = $request->validate([
            'role' => ['sometimes', 'string', 'max:255'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer'],
        ]);

        if (isset($data['role'])) {
            $role->role = $data['role'];
            $role->save();
        }

        if (array_key_exists('permission_ids', $data)) {
            $role->permissions()->sync($this->validPermissionIds($data['permission_ids'] ?? [], $companyId));
        }

        return $this->json(true, 'Role berhasil diupdate.', ['role' => $role->fresh('permissions')]);
    }

    public function destroyRole(Request $request, int $id)
    {
        $companyId = $this->companyId($request);
        $role = Role::withoutGlobalScopes()->where('company_id', $companyId)->findOrFail($id);

        if ($role->users()->exists()) {
            return $this->json(false, 'Role masih dipakai user.', null, 422);
        }

        $role->delete();

        return $this->json(true, 'Role berhasil dihapus.');
    }

    public function permissions(Request $request)
    {
        $companyId = $this->companyId($request);
        $categories = PermissionCategory::withoutGlobalScopes()
            ->with(['permissions' => fn ($q) => $q->orderBy('name')])
            ->when(Schema::hasColumn('permission_categories', 'company_id'), fn ($q) => $q->where('company_id', $companyId))
            ->orderBy('display_order')
            ->get();

        return $this->json(true, 'Permission list.', ['permission_categories' => $categories]);
    }

    public function mobileMenu(Request $request)
    {
        $user = $request->user();
        // Super admin may restore a global session before selecting a company.
        $companyId = $user->isSuperAdmin() ? ($request->query('company_id') ?: null) : $this->companyId($request);
        $permissionRoutes = $user->isSuperAdmin()
            ? Permission::pluck('route')->all()
            : app(\App\Services\MobileAccess::class)->routes($user);

        $categories = PermissionCategory::withoutGlobalScopes()
            ->with(['permissions' => fn ($q) => $q->orderBy('route')])
            ->when(Schema::hasColumn('permission_categories', 'company_id'), fn ($q) => $q->where('company_id', $companyId))
            ->orderBy('display_order')
            ->get()
            ->map(function (PermissionCategory $category) use ($permissionRoutes) {
                $routes = $category->permissions->pluck('route')->all();

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'icon' => $category->icon,
                    'feature_key' => $category->feature_key,
                    'can_view' => $this->matchesAny($routes, $permissionRoutes, ['listing', 'sell', 'reconciliation']),
                    'can_create' => $this->matchesAny($routes, $permissionRoutes, ['add', 'store']),
                    'can_update' => $this->matchesAny($routes, $permissionRoutes, ['edit', 'update']),
                    'can_delete' => $this->matchesAny($routes, $permissionRoutes, ['delete', 'destroy', 'force-delete']),
                    'routes' => array_values(array_intersect($routes, $permissionRoutes)),
                ];
            })
            ->filter(fn ($menu) => $menu['can_view'] || $menu['can_create'] || $menu['can_update'] || $menu['can_delete'])
            ->values();

        return $this->json(true, 'Mobile menu permission.', ['menus' => $categories, 'permissions' => app(\App\Services\MobileAccess::class)->permissions($user)]);
    }

    private function userRecord(Request $request, int $id): User
    {
        return User::withoutGlobalScopes()
            ->with(['company', 'role', 'store'])
            ->where('company_id', $this->companyId($request))
            ->where('is_super_admin', false)
            ->findOrFail($id);
    }

    private function assertRoleStore(array $data, int $companyId): void
    {
        if (! empty($data['role_id'])) {
            abort_if(! Role::withoutGlobalScopes()->where('company_id', $companyId)->whereKey($data['role_id'])->exists(), 422, 'Role tidak valid untuk company ini.');
        }

        if (! empty($data['store_id'])) {
            abort_if(! Store::where('company_id', $companyId)->whereKey($data['store_id'])->exists(), 422, 'Store tidak valid untuk company ini.');
        }
    }

    private function validPermissionIds(array $ids, int $companyId): array
    {
        if (empty($ids)) {
            return [];
        }

        return Permission::withoutGlobalScopes()
            ->when(Schema::hasColumn('permissions', 'company_id'), fn ($q) => $q->where('company_id', $companyId))
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();
    }

    private function companyId(Request $request): int
    {
        if ($request->user()?->isSuperAdmin()) {
            return (int) ($request->query('company_id') ?: $request->input('company_id') ?: abort(422, 'company_id wajib untuk Super Admin.'));
        }

        return (int) $request->user()->company_id;
    }

    private function matchesAny(array $routes, array $allowedRoutes, array $needles): bool
    {
        foreach (array_intersect($routes, $allowedRoutes) as $route) {
            foreach ($needles as $needle) {
                if (str_contains($route, $needle)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function json(bool $success, string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(compact('success', 'message', 'data'), $status);
    }
}
