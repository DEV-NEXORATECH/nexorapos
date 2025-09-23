<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;
use App\Models\User;

class Role extends Model
{
    protected $table = 'roles';

    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'role_permissions','role_id','permission_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
