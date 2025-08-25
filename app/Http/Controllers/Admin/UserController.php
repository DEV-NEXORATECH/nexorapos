<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\UserService;
class UserController extends Controller
{
  public $userService;

  public function __construct()
  {
    $this->userService=new UserService();
  }

  public function index(){
    return $this->userService->index();
  }

  public function post()
  {
    return $this->userService->post();
  }

  public function store(Request $request)
  {
    return $this->userService->store($request);
  }

  public function edit(Request $request, $id){
    return $this->userService->edit($request,$id);
  }

  public function update(Request $request,$id){
    return $this->userService->update($request,$id);
  }

  public function destroy(Request $request,$id){
    return $this->userService->destroy($request,$id);
  }
}
