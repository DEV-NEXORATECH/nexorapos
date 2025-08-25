<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\PermissionService;
class PermissionController extends Controller
{
    public $permissionService;
    public function __construct(){
        $this->permissionService=new PermissionService();
    }
    public function index(){
        return $this->permissionService->index();
    }

    public function add()
    {
        return $this->permissionService->add();
    }

    public function store(Request $request){
        return $this->permissionService->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->permissionService->edit($request, $id);
    }

    public function update(Request $request, $id)
    {
        return $this->permissionService->update($request,$id);
    }

    public function destroy(Request $request, $id){
        return $this->permissionService->destroy($request, $id);
    }
}
