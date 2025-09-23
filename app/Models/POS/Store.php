<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table='stores';
    protected $fillable=[
        'namme',
        'code',
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
