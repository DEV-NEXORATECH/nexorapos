<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_features', 'feature_key', 'company_id', 'key', 'id')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }
}
