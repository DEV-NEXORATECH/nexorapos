<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\RoleUserService;
class RoleUserController extends Controller
{
    public $roleUserService;

    public function __construct()
    {
        $this->roleUserService=new RoleUserService();
    }

    public function index()
    {
        return $this->roleUserService->index();
    }

    public function add()
    {
        return $this->roleUserService->add();
    }

    public function store(Request $request)
    {
        return $this->roleUserService->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->roleUserService->edit($request,$id);
    }

    public function update(Request $request, $id)
    {
        return $this->roleUserService->update($request,$id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->roleUserService->destroy($request,$id);
    }
}
