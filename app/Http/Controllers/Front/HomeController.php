<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Front\HomeService;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
class HomeController extends Controller
{
    public $homeservice;

        public function __construct()
        {
            $this->homeService = new HomeService();
        }

        public function index()
        {
            return $this->homeService->index();
        }

        public function archive()
        {
            return $this->homeService->archive();
        }

        public function category()
        {
            return $this->homeService->category();
        }

        public function blogDetail()
        {
            return $this->homeService->blogDetail();
        }

        public function contact()
        {
            return $this->homeService->contact();
        }

        public function blogdetails($id)
        {
            $post=Post::with('categories', 'users')->find($id);

            return view('front.blog_detail')->with('post', $post);
        }

        public function store(Request $request)
      {

            return $this->homeService->store($request);

      }
}
