<?php
namespace App\Pos\SaleItem;
use Illuminate\Support\Facades\Validator;
class SaleItemValidation{
    public function validate($inputs)
    {
        $validator=Validator::make($inputs,[
            'sale_id' => 'required',
            'product_id' => 'required',
            'product_variant_id' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'discount' => 'required',
            'tax' => 'required',
        ]);
        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
