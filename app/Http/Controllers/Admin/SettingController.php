<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\SettingService;
class SettingController extends Controller
{

   public $settingService;

   public function __construct()
   {
    $this->settingService=new SettingService();
   }

   public function index()
   {
    return $this->settingService->index();
   }

   public function add()
   {
    return $this->settingService->add();
   }

   public function store(Request $request)
   {
    return $this->settingService->store($request);
   }

   public function edit(Request $request, $id)
   {
    return $this->settingService->edit($request,$id);
   }

   public function update(Request $request, $id)
   {
    return $this->settingService->update($request,$id);
   }

   public function destroy(Request $request,$id)
   {
    return $this->settingService->destroy($request,$id);
   }
}
