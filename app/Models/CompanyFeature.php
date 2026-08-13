<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class CompanyFeature extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'feature_key',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_key', 'key');
    }
}
