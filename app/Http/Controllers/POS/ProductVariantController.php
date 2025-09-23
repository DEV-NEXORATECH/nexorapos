<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\ProductVariantService;
class ProductVariantController extends Controller
{
    public $variant;
    public function __construct()
    {
        return $this->variant=new ProductVariantService();
    }
    public function index()
    {
        return $this->variant->index();
    }

    public function add()
    {
        return $this->variant->add();
    }

    public function store(Request $request)
    {
        return $this->variant->store($request);
    }

    public function edit($id)
    {
        return $this->variant->edit($id);
    }

    public function update(Request $request, $id)
    {
        return $this->variant->update($request,$id);
    }

    public function destroy($id)
    {
        return $this->variant->destroy($id);
    }
}
