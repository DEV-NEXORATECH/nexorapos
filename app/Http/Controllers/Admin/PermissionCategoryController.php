<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\PermissionCategoryService;

class PermissionCategoryController extends Controller
{
    public $permissionCategoryService;
    public function __construct(){
        return $this->permissionCategoryService=new PermissionCategoryService();
    }
    public function index()
    {
        return $this->permissionCategoryService->index();
    }

    public function add()
    {
        return $this->permissionCategoryService->add();

    }

    public function store(Request $request)
    {
         return $this->permissionCategoryService->store($request);
    }
    public function edit(Request $request,$id)
    {
         return $this->permissionCategoryService->edit($request,$id);
    }

    public function update(Request $request,$id)
    {
         return $this->permissionCategoryService->update($request,$id);
    }

    public function destroy(Request $request,$id)
    {
        return $this->permissionCategoryService->destroy($request,$id);
    }
}
