<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\PaymentService;
class PaymentController extends Controller
{
    public $payment;
    public function __construct()
    {
        return $this->payment=new PaymentService();
    }

    public function index()
    {
        return $this->payment->index();
    }

    public function add()
    {
        return $this->payment->add();
    }

    public function store(Request $request)
    {
        return $this->payment->store($request);
    }

    public function edit($id)
    {
        return $this->payment->edit($id);
    }

    public function update(Request $request, $id){
        return $this->payment->update($request,$id);
    }

    public function destroy($id)
    {
        return $this->payment->destroy($id);
    }
}
