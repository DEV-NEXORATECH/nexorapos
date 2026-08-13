<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;
use App\Models\Role;

class Permission extends Model
{
    use BelongsToCompany;

    protected $table = 'permissions';
    protected $fillable = [
        'name',
        'route',
        'icons',
        'permission_category_id',
        'company_id',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class,'role_permissions','permission_id','role_id');
    }

    public function permission_category()
    {
        return $this->belongsTo(PermissionCategory::class);
    }
}
