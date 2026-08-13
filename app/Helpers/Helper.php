<?php

use App\Models\PermissionCategory;
use App\Models\CompanyFeature;
use Illuminate\Support\Facades\Schema;

if (!function_exists('modules')) {
    function modules() {
        $user = auth()->user();
        $permissionColumns = ['id', 'permission_category_id', 'name', 'route'];

        if (Schema::hasColumn('permissions', 'icon')) {
            $permissionColumns[] = 'icon';
        }

        if (Schema::hasColumn('permissions', 'is_show')) {
            $permissionColumns[] = 'is_show';
        }

        $modules = PermissionCategory::with(['permissions' => function($query) use ($permissionColumns) {
            $query->select($permissionColumns);
        }])
        ->orderBy('display_order', 'asc')
        ->get();

        $modules->each(function ($category) {
            $visiblePermissions = $category->permissions->filter(function ($permission) {
                if (! array_key_exists('is_show', $permission->getAttributes())) {
                    return true;
                }

                return (bool) $permission->is_show;
            });

            $category->setRelation('permissions', $visiblePermissions);
        });

        if ($user && ! $user->isSuperAdmin()) {
            $enabledFeatureKeys = CompanyFeature::where('company_id', $user->company_id)
                ->where('is_enabled', true)
                ->pluck('feature_key')
                ->all();

            $modules = $modules->filter(function ($category) use ($enabledFeatureKeys) {
                $featureKey = $category->getAttribute('feature_key');

                if (! $featureKey) {
                    return true;
                }

                return in_array($featureKey, $enabledFeatureKeys, true);
            });
        }

        return $modules;
    }
}
