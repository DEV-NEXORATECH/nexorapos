<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPayment extends Model
{
    protected $fillable = [
        'company_id',
        'order_id',
        'amount',
        'status',
        'payment_type',
        'transaction_id',
        'fraud_status',
        'snap_token',
        'paid_at',
        'raw_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'raw_response' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
