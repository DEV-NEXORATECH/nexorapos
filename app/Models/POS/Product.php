<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToCompany;
class Product extends Model
{
 use BelongsToCompany;
 use SoftDeletes;
 protected $fillable = [
    'name',
    'sku',
    'barcode',
    'image',
    'category_id',
    'tax_id',
    'price',
    'cost',
    'track_stock',
    'stock_quantity',
    'minimum_stock',
    'company_id',
   ];
   public function tax()
   {
    return $this->belongsTo(Tax::class);
   }
    public function category()
    {
     return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function purchase_items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class);
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
