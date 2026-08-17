<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileApiToken extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'token_hash',
        'refresh_token_hash',
        'abilities',
        'last_used_at',
        'expires_at',
        'refresh_expires_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'refresh_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
