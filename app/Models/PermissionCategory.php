<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionCategory extends Model
{
    // One category has many permissions
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_category_id');
    }
}

