<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Sale extends Model
{
    protected $table='sales';

    protected $fillables=[
        'store_id',
        'customer_id',
        'user_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function sale_items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


}
