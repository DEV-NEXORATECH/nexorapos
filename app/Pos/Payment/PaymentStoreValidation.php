<?php
namespace App\Pos\Payment;
use Illuminate\Support\Facades\Validator;
class PaymentStoreValidation{
    public function validate($inputs){
        $validator=Validator::make($inputs, [
            'sale_id' => 'required',
            'method_id' => 'required',
            'amount' => 'required',
            'reference' => 'required',
        ]);
        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
