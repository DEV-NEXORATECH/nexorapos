<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
 use SoftDeletes;
 protected $fillable = [
    'name',
    'code',
    'category_id',
    'tax_id',
    'price',
    'cost',
    'unit',
    'alert_quantity',
    'image',
    'details',
    'is_active',
    'created_by',
    'updated_by',
    'deleted_at',
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
