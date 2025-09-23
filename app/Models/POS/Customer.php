<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
