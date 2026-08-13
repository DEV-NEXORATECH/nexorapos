<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\CustomerService;
class CustomerController extends Controller
{
     public $customerService;

    public function __construct()
    {
        $this->customerService=new CustomerService();
    }

    public function index()
    {

        return $this->customerService->index();
    }

    public function add()
    {
        return $this->customerService->add();
    }

    public function store(Request $request)
    {
        return $this->customerService->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->customerService->edit($request,$id);
    }

    public function show(Request $request, $id)
    {
        return $this->customerService->show($request, $id);
    }

    public function update(Request $request, $id)
    {
        return $this->customerService->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->customerService->destroy($id);
    }
}
