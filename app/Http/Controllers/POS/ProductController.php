<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\ProductService;
class ProductController extends Controller
{
    public $productService;
    public function __construct()
    {
        $this->productService=new ProductService();
    }
   public function index(){
    return $this->productService->index();
   }

   public function add()
   {
    return $this->productService->add();
   }

   public function store(Request $request)
   {
    return $this->productService->store($request);
   }

   public function edit(Request $request, $id)
   {
    return $this->productService->edit($request,$id);
   }

   public function update(Request $request, $id)
   {
    return $this->productService->update($request,$id);
   }

   public function destroy($id)
   {
    return $this->productService->destroy($id);
   }

   public function export()
   {
    return $this->productService->export();
   }

    public function import(Request $request)
    {
        return $this->productService->import($request);
    }
    //product Soft delete listing
    public function deletedProducts()
    {
        return $this->productService->deletedProducts();
    }
    public function restore($id)
    {
        return $this->productService->restore($id);
    }
    public function forceDelete($id)
    {
        return $this->productService->forceDelete($id);
    }
}
