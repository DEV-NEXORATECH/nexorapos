<?php
namespace App\Pos\Product;
use Illuminate\Http\Request;
use App\Models\Pos\Product;
use Illuminate\Support\Facades\Validator;
class ProductStoreValidation
{
    public function validate($inputs){
        $validator=Validator::make($inputs,[
            'name'=>'required|unique:products,name',
            'tax_id'=>'required|exists:taxes,id',
            'sku'=>'nullable|unique:products,sku',
            'barcode'=>'nullable|unique:products,barcode',
            'price'=>'required|numeric|min:0',
            'cost'=>'nullable|numeric|min:0',
            'track_stock'=>'required|in:0,1'
        ]);
        if($validator->fails()){
            return $validator->errors();
        }
    }
}
