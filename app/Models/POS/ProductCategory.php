<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class ProductCategory extends Model
{
    use BelongsToCompany;

    protected $table = 'product_categories';
    protected $fillable = ['name', 'company_id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}


