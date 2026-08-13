<?php
namespace App\Pos\Sale;
use Illuminate\Support\Facades\Validator;
class SaleStoreValidation{
    public function validate($inputs){
        $validator=Validator::make($inputs, [
            'store_id' => 'required',
            'customer_id' => 'nullable',
            'user_id' => 'required',
            'subtotal' => 'required',
            'tax' => 'nullable',
            'discount' =>'nullable',
            'total' => 'required',
            'status' => 'nullable',
        ]);
        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
