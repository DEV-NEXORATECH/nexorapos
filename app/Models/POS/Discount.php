<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class Discount extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'type',
        'value',
        'company_id',
    ];
}
