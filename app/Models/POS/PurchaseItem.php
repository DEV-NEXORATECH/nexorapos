<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class PurchaseItem extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'cost',
        'company_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
