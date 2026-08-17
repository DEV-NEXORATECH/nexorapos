<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class StockMovement extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'store_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'type',
        'status',
        'voided_at',
        'note',
        'company_id',
    ];

    protected $casts = [
        'voided_at' => 'datetime',
    ];

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
