<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pos\TaxService;
class TaxController extends Controller
{
    public $taxService;
    public function __construct()
    {
        $this->taxService = new TaxService();
    }

    public function index()
    {
        return $this->taxService->index();
    }

    public function add(){
        return $this->taxService->add();
    }

    public function store(Request $request)
    {
        return $this->taxService->store($request);
    }

    public function edit(Request $request,$id)
    {
        return $this->taxService->edit($request,$id);
    }

    public function update(Request $request, $id)
    {
        return $this->taxService->update($request, $id);
    }

    public function destroy(Request $request,$id)
    {
        return $this->taxService->destroy($request,$id);
    }
}
