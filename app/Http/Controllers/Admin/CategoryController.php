<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
   public $categoryService;

   public function __construct()
   {
    $this->categoryService=new CategoryService();
   }

   public function category()
   {
    return $this->categoryService->category();
   }

   public function store(Request $request)
   {
    return $this->categoryService->store($request);
   }

   public function index()
   {
    return $this->categoryService->index();
   }

   public function destroy(Request $request,$id)
   {
    return $this->categoryService->destroy($request,$id);
   }

   public function edit(Request $request, $id)
   {
    return $this->categoryService->edit($request, $id);
   }

   public function update(Request $request, $id)
   {
    return $this->categoryService->update($request, $id);
   }
}
