<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class Method extends Model
{
    public function Payments()
    {
        return $this->hasMany(Payment::class);
    }
}
