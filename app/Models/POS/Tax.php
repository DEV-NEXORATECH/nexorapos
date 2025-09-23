<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    // Add any relationships or methods if necessary
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_taxes','product_id', 'id');
    }
}
