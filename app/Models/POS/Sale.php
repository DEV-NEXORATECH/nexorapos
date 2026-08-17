<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Concerns\BelongsToCompany;
class Sale extends Model
{
    use BelongsToCompany;

    protected $table='sales';

    protected $fillable=[
        'store_id',
        'customer_id',
        'user_id',
        'cashier_shift_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'paid_amount',
        'change_amount',
        'status',
        'payment_status',
        'invoice_number',
        'idempotency_key',
        'cancelled_at',
        'void_reason',
        'voided_by',
        'company_id',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
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

    public function cashier_shift()
    {
        return $this->belongsTo(CashierShift::class);
    }


}
