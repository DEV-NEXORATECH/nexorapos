<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\StockService;
class StockController extends Controller
{
    public $stock;
    public function __construct()
    {
        return $this->stock=new StockService();
    }

    public function index()
    {
        return $this->stock->index();
    }

    public function add()
    {
        return $this->stock->add();
    }

    public function store(Request $request)
    {
        return $this->stock->store($request);
    }

    public function edit($id)
    {
        return $this->stock->edit($id);
    }

    public function update(Request $request, $id)
    {
        return $this->stock->update($request,$id);
    }

    public function destroy($id)
    {
        return $this->stock->destroy($id);
    }
}
