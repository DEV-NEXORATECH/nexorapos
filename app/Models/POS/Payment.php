<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function method()
    {
        return $this->belongsTo(Method::class);
    }
}
