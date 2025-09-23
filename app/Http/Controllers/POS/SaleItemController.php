<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\SaleItemService;
class SaleItemController extends Controller
{
    public $sale;
    public function __construct()
    {
        return $this->sale=new SaleItemService();
    }

    public function index()
    {
        return $this->sale->index();
    }

    public function add()
    {
        return $this->sale->add();
    }

    public function store(Request $request)
    {
        return $this->sale->store($request);
    }

    public function edit($id)
    {
        return $this->sale->edit($id);
    }

    public function update(Request $request,$id){
        return $this->sale->update($request,$id);
    }

    public function destroy($id)
    {
        return $this->sale->destroy($id);
    }
}
