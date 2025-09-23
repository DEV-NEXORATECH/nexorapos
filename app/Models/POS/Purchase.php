<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public function supplier()
    {
        return $this->belongsTO(Supplier::class);
    }

    public function purchase_items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
