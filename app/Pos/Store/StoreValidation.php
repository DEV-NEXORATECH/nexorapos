<?php
namespace App\Pos\Store;
use Illuminate\Support\Facades\Validator;
class StoreValidation{
   public function validate($inputs){
    $validator=Validator::make($inputs, [
    'name' => 'required|string',
    'code' => 'required',
    ]);
    if($validator->fails()){
        return $validator->errors()->first();
    }
   }
}
