<?php
namespace App\Pos\Item;
use Illuminate\Support\Facades\Validator;
class ItemStoreValidation{
    public function validate($inputs){
        $validator=Validator::make($inputs, [
            'purchase_id'=> 'required',
            'product_id' => 'required',
            'quantity' => 'required',
            'cost' => 'required'
        ]);
        if($validator->fails())
        {
            return $validator->errors()->first();
        }
    }
}
