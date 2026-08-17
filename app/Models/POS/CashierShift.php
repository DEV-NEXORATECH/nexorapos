<?php

namespace App\Models\Pos;

use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'store_id',
        'user_id',
        'shift_name',
        'opening_cash',
        'closing_cash',
        'expected_cash',
        'cash_difference',
        'sales_count',
        'sales_total',
        'opened_at',
        'closed_at',
        'status',
        'note',
    ];

    protected $casts = [
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'sales_total' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
