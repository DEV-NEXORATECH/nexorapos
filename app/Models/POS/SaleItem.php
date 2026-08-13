<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class SaleItem extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price',
        'discount',
        'tax',
        'company_id',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
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
