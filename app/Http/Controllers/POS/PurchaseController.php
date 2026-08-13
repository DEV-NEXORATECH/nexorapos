<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\PurchaseService;
class PurchaseController extends Controller
{
     public $purchaseService;
    public function __construct()
    {
        $this->purchaseService=new PurchaseService();
    }
   public function index(){
    return $this->purchaseService->index();
   }

   public function add()
   {
    return $this->purchaseService->add();
   }

   public function store(Request $request)
   {
    return $this->purchaseService->store($request);
   }

   public function edit($id)
   {
    return $this->purchaseService->edit($id);
   }

   public function update(Request $request, $id)
   {
    return $this->purchaseService->update($request,$id);
   }

   public function destroy($id)
   {
    return $this->purchaseService->destroy($id);
   }

   public function show($id)
   {
       return $this->purchaseService->show($id);
   }

}
