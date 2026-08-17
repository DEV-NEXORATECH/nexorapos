<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class ProductVariant extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'barcode',
        'price',
        'cost',
        'company_id',
    ];

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
