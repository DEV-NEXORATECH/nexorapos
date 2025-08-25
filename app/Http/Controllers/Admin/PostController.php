<?php

namespace App\Http\Controllers\Admin;
use App\Services\Admin\PostService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{


  public $postService;

    public function __construct()
    {
        $this->postService= new PostService();
    }

    public function index(Request $request)
    {
        return $this->postService->index($request);
    }

    public function post()
    {
        return $this->postService->post();
    }

    public function store(Request $request)
    {
        return $this->postService->store($request);
    }

    public function destroy(Request $request, $id){
        return $this->postService->destroy($request,$id);
    }

    public function edit(Request $request,$id){
        return $this->postService->edit($request,$id);
    }

    public function update(Request $request,$id){
        return $this->postService->update($request,$id);
    }
}
