<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\RoleService;

class RoleController extends Controller
{
     public $roleService;

    public function __construct(){
        $this->roleService=new RoleService();
    }

    public function index(){
        return $this->roleService->index();
    }

    public function add()
    {
        return $this->roleService->add();
    }

    public function store(Request $request){
        return $this->roleService->store($request);
    }

    public function edit(Request $request, $id){
        return $this->roleService->edit($request, $id);
    }

    public function update(Request $request, $id){
        return $this->roleService->update($request, $id);
    }

    public function destroy(Request $request, $id){
        return $this->roleService->destroy($request,$id);
    }
}
