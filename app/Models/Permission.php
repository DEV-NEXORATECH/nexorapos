<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role;

class Permission extends Model
{
    protected $table = 'permissions';

    public function roles()
    {
        return $this->belongsToMany(Role::class,'role_permissions','permission_id','role_id');
    }

    public function permission_category()
    {
        return $this->belongsTo(PermissionCategory::class);
    }
}
