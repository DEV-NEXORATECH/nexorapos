<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table='suppliers';
    protected $fillable=[
        'name',
        'email',
        'phone',
        'address',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class,);
    }
}
