<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
