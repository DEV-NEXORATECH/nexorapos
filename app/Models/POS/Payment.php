<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class Payment extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'sale_id',
        'method_id',
        'amount',
        'reference',
        'status',
        'voided_at',
        'company_id',
    ];

    protected $casts = [
        'voided_at' => 'datetime',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function method()
    {
        return $this->belongsTo(Method::class);
    }
}
