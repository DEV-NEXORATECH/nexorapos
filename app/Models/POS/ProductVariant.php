<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock_movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function sale_items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
