<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
