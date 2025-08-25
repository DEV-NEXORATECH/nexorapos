<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
    'user_id',
    'category_id',
    'title',
    'content',
    'image'
];

public function users()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function categories()
{
    return $this->belongsTo(Category::class, 'category_id');
}

}
