<?php
namespace App\Pos\Stock;
use Illuminate\Support\Facades\Validator;

class StockStoreValidation{
    public function validate($inputs){
        $validator=Validator::make($inputs, [
            'store_id' =>'required',
            'product_id' => 'required',
            'product_variant_id' => 'nullable',
            'quantity' => 'required|numeric|min:0',
            'type' => 'required',
        ]);

        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
