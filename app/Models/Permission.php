<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    // Corrected relation
    public function permission_category()
    {
        return $this->belongsTo(PermissionCategory::class, 'permission_category_id');
    }
}
