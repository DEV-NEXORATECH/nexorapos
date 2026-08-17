<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class Purchase extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'supplier_id',
        'reference',
        'total_amount',
        'status',
        'cancelled_at',
        'company_id',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTO(Supplier::class);
    }

    public function purchase_items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
