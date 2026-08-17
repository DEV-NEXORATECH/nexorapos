<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class Customer extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'customer_code',
        'company_id',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalSpendAttribute()
    {
        return $this->sales()->where('status', 'completed')->sum('total');
    }

    public function getTotalTransactionsAttribute()
    {
        return $this->sales()->count();
    }
}
