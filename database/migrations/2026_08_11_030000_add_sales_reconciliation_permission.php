<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permission_categories') || ! Schema::hasTable('permissions')) {
            return;
        }

        $companies = Schema::hasTable('companies') ? DB::table('companies')->pluck('id') : collect([null]);

        foreach ($companies as $companyId) {
            $categoryId = $this->ensureSalesCategory($companyId);
            $permissionId = $this->ensureReconciliationPermission($categoryId, $companyId);
            $this->assignToAdminRoles($permissionId, $companyId);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permissions')) {
            DB::table('permissions')->where('route', 'admin-panel/sales/reconciliation')->delete();
        }
    }

    private function ensureSalesCategory(?int $companyId): int
    {
        $query = DB::table('permission_categories')->where('name', 'like', '%Sale%');

        if (Schema::hasColumn('permission_categories', 'company_id')) {
            $query->where('company_id', $companyId);
        }

        $salesCategory = $query->orderBy('id')->first();

        if ($salesCategory) {
            $featureKey = $salesCategory->feature_key ?? null;

            if (Schema::hasColumn('permission_categories', 'feature_key') && ! $featureKey) {
                DB::table('permission_categories')
                    ->where('id', $salesCategory->id)
                    ->update(['feature_key' => Str::slug($salesCategory->name, '_')]);
            }

            return $salesCategory->id;
        }

        $payload = [
            'name' => 'Sales',
            'display_order' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('permission_categories', 'feature_key')) {
            $payload['feature_key'] = 'sales';
        }

        if (Schema::hasColumn('permission_categories', 'company_id')) {
            $payload['company_id'] = $companyId;
        }

        if (Schema::hasColumn('permission_categories', 'icon')) {
            $payload['icon'] = 'la la-shopping-cart';
        }

        if (Schema::hasColumn('permission_categories', 'icons')) {
            $payload['icons'] = 'la la-shopping-cart';
        }

        return DB::table('permission_categories')->insertGetId($payload);
    }

    private function ensureReconciliationPermission(int $categoryId, ?int $companyId): int
    {
        $lookup = [
            'permission_category_id' => $categoryId,
            'route' => 'admin-panel/sales/reconciliation',
        ];

        if (Schema::hasColumn('permissions', 'company_id')) {
            $lookup['company_id'] = $companyId;
        }

        $payload = [
            'permission_category_id' => $categoryId,
            'name' => 'Daily Reconciliation',
            'route' => 'admin-panel/sales/reconciliation',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('permissions', 'company_id')) {
            $payload['company_id'] = $companyId;
        }

        if (Schema::hasColumn('permissions', 'is_show')) {
            $payload['is_show'] = 1;
        }

        DB::table('permissions')->updateOrInsert($lookup, $payload);

        return DB::table('permissions')
            ->when(Schema::hasColumn('permissions', 'company_id'), fn ($query) => $query->where('company_id', $companyId))
            ->where('permission_category_id', $categoryId)
            ->where('route', 'admin-panel/sales/reconciliation')
            ->value('id');
    }

    private function assignToAdminRoles(int $permissionId, ?int $companyId): void
    {
        if (! Schema::hasTable('role_permissions') || ! Schema::hasTable('roles')) {
            return;
        }

        DB::table('roles')
            ->where('role', 'like', '%Admin%')
            ->when(Schema::hasColumn('roles', 'company_id'), fn ($query) => $query->where('company_id', $companyId))
            ->pluck('id')
            ->each(function ($roleId) use ($permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permissionId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            });
    }
};
