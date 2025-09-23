<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\SupplierService;

class SupplierController extends Controller
{
    public $supplierService;

    public function __construct()
    {
        $this->supplierService=new SupplierService();
    }

    public function index()
    {

        return $this->supplierService->index();
    }

    public function add()
    {
        return $this->supplierService->add();
    }

    public function store(Request $request)
    {
        return $this->supplierService->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->supplierService->edit($request,$id);
    }

    public function update(Request $request, $id)
    {
        return $this->supplierService->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->supplierService->destroy($id);
    }
}
