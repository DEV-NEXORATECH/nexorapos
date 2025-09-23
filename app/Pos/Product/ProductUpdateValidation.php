<?php
namespace App\Pos\Product;
use Illuminate\Support\Facades\Validator;
use App\Models\Pos\Product;
class ProductUpdateValidation{
    public function validate($inputs,$id){
        $validator=Validator::make($inputs,[
            'name'=>'required|string|max:255',
            'tax_id'=>'required|exists:taxes,id',
            'sku'=>'required|string|max:100|unique:products,sku,'.$id,
            'barcode'=>'nullable|string|max:100|unique:products,barcode,'.$id,
            'price'=>'required|numeric|min:0',
            'cost'=>'nullable|numeric|min:0',
            'track_stock'=>'required|boolean'
        ]);
        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
