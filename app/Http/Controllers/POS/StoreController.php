<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\StoreService;
class StoreController extends Controller
{
    public $store;
    public function __construct()
    {
        $this->store=new StoreService();
    }
    public function index()
    {
        return $this->store->index();
    }

    public function add()
    {
        return $this->store->add();
    }
    
    public function show($id)
    {
        return $this->store->show($id);
    }

    public function store(Request $request)
    {
        return $this->store->store($request);
    }

    public function edit($id)
    {
        return $this->store->edit($id);
    }

    public function update(Request $request, $id)
    {
        return $this->store->update($request,$id);
    }

    public function destroy($id)
    {
        return $this->store->destroy($id);
    }
}
