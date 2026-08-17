<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class Supplier extends Model
{
    use BelongsToCompany;

    protected $table='suppliers';
    protected $fillable=[
        'name',
        'email',
        'phone',
        'address',
        'company_id',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class,);
    }
}
