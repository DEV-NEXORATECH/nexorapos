<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\CategoryService;
class CategoryController extends Controller
{
    public $categoryService;
    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }

    public function index(){
        return $this->categoryService->index();
    }

    public function add()
    {
        return $this->categoryService->add();
    }

    public function store(Request $request)
    {
        return $this->categoryService->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->categoryService->edit($request, $id);
    }

    public function update(Request $request, $id)
    {
        return $this->categoryService->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->categoryService->destroy($id);
    }
}
