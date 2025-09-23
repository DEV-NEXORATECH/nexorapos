<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\SaleService;
class SaleController extends Controller
{
    public $sale;
    public function __construct()
    {
        return $this->sale=new SaleService();
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

    public function update(Request $request,$id)
    {
        return $this->sale->update($request,$id);
    }

    public function destroy($id)
    {
        return $this->sale->destroy($id);
    }

    // POS endpoints
    public function pos()
    {
        return $this->sale->pos();
    }

    public function addToCart(Request $request)
    {
        return $this->sale->addToCart($request);
    }

    public function updateCart(Request $request)
    {
        return $this->sale->updateCart($request);
    }

    public function removeFromCart(Request $request)
    {
        return $this->sale->removeFromCart($request);
    }

    public function checkout(Request $request)
    {
        return $this->sale->checkout($request);
    }

    public function invoice($id)
    {
        return $this->sale->invoice($id);
    }
    public function pdf($id)
    {
        return $this->sale->pdf($id);
    }
}
