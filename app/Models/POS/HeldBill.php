<?php

namespace App\Models\Pos;

use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class HeldBill extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'store_id',
        'user_id',
        'customer_id',
        'bill_name',
        'table_name',
        'cart_snapshot',
        'totals_snapshot',
        'status',
        'restored_at',
    ];

    protected $casts = [
        'cart_snapshot' => 'array',
        'totals_snapshot' => 'array',
        'restored_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
