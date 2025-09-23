<?php

namespace App\Http\Controllers\POS;
use App\Services\Pos\ItemService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    public $item;
    public function __construct()
    {
        $this->item= new ItemService();
    }
    public function index()
    {
        return $this->item->index();
    }

    public function add()
    {
        return $this->item->add();
    }

    public function store(Request $request)
    {
        return $this->item->store($request);
    }

    public function edit($id)
    {
        return $this->item->edit($id);
    }


    public function update(Request $request,$id)
    {
        return $this->item->update($request,$id);
    }

    public function destroy($id)
    {
        return $this->item->destroy($id);
    }
}
