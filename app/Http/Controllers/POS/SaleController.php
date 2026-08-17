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

    public function reconciliation(Request $request)
    {
        return $this->sale->reconciliation($request);
    }

    public function shiftReport(Request $request)
    {
        return $this->sale->shiftReport($request);
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

    public function addCustomAmount(Request $request)
    {
        return $this->sale->addCustomAmount($request);
    }

    public function updateCart(Request $request)
    {
        return $this->sale->updateCart($request);
    }

    public function removeFromCart(Request $request)
    {
        return $this->sale->removeFromCart($request);
    }

    public function holdBill(Request $request)
    {
        return $this->sale->holdBill($request);
    }

    public function restoreHeldBill(Request $request, $id)
    {
        return $this->sale->restoreHeldBill($request, $id);
    }

    public function deleteHeldBill(Request $request, $id)
    {
        return $this->sale->deleteHeldBill($request, $id);
    }

    public function openShift(Request $request)
    {
        return $this->sale->openShift($request);
    }

    public function closeShift(Request $request)
    {
        return $this->sale->closeShift($request);
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
