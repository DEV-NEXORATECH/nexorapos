<?php

use App\Models\PermissionCategory;

if (!function_exists('modules')) {
    function modules() {
        // Eager load 'permissions' but only where is_show = 1
        $modules = PermissionCategory::with(['permissions' => function($query) {
            $query->where('is_show', 1);
        }])
        ->orderBy('display_order', 'asc')
        ->get();

        return $modules;
    }
}

