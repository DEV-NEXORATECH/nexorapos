<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class Method extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'company_id',
    ];

    public function Payments()
    {
        return $this->hasMany(Payment::class);
    }
}
