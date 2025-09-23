<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\DiscountService;
class DiscountCountroller extends Controller
{
    public $discount;
    public function __construct()
    {
        return $this->discount=new DiscountService();
    }
    public function index()
    {
        return $this->discount->index();
    }

    public function add()
    {
        return $this->discount->add();
    }

    public function store(Request $request)
    {
        return $this->discount->store($request);
    }

    public function edit($id){
        return $this->discount->edit($id);
    }

    public function update(Request $request, $id){
        return $this->discount->update($request,$id);
    }

    public function destroy($id){
        return $this->discount->destroy($id);
    }
}
