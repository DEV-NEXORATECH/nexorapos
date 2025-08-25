<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
if (!function_exists('categories')) {
    function categories() {
        return Category::all();
    }
}

if (!function_exists('writerPosts')) {
    function writerPosts($perPage = 5) { // Default 5 posts per page
        return Post::with('users', 'categories')->paginate($perPage);
    }
}

if(!function_exists('settings')){
    function settings(){
        return Setting::all();
    }
}
