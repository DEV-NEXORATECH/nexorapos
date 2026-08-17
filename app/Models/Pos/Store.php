<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class Store extends Model
{
    use BelongsToCompany;

    protected $table='stores';
    protected $fillable=[
        'name',
        'code',
        'company_id',
    ];
    public function stock_movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
