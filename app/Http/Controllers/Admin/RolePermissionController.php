<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\RolePermissionService;
use App\Models\RolePermission;
class RolePermissionController extends Controller
{
    public $rolePermission;
    public function __construct()
    {
        $this->rolePermission= new RolePermissionService();
    }

    public function index()
    {
        return $this->rolePermission->index();
    }

    public function add()
    {
        return $this->rolePermission->add();
    }

    public function store(Request $request)
    {
        return $this->rolePermission->store($request);
    }

    public function destroy(Request $request,$id)
    {
        return $this->rolePermission->destroy($request,$id);
    }

    public function edit(Request $request,$id)
    {
        return $this->rolePermission->edit($request,$id);
    }

    public function update(Request $request, $id)
    {
        return $this->rolePermission->update($request,$id);
    }
}
