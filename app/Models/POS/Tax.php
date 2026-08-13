<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class Tax extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'rate',
        'company_id',
    ];

    // Add any relationships or methods if necessary
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_taxes','product_id', 'id');
    }
}
