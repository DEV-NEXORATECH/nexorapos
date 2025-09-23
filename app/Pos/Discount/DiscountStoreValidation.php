<?php
namespace App\Pos\Discount;
use Illuminate\Support\Facades\Validator;
class DiscountStoreValidation{
    public function validate($inputs){
        $validator=Validator::make($inputs,[
            'name' => 'required',
            'type' => 'required',
            'value' => 'required'
        ]);
        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
