<?php

namespace App\Services\Admin;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Models\Post;
class AdminService
{
    public function adminPanel()
    {
        $userCount=User::count();
        $postCount=Post::count();
        return view('admin.dashboard', compact('userCount','postCount'));
    }


}
