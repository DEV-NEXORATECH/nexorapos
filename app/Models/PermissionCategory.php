<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class PermissionCategory extends Model
{
    use BelongsToCompany;

    protected $fillable = ['name', 'icon', 'display_order', 'feature_key', 'company_id'];

    // One category has many permissions
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_category_id');
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_key', 'key');
    }
}
