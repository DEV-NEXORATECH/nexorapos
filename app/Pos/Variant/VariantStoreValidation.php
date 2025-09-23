<?php
namespace App\Pos\Variant;
use Illuminate\Support\Facades\Validator;

class VariantStoreValidation{
    public function validate($inputs)
    {
        $validator=Validator::make($inputs,[
            'product_id' => 'required',
            'name' => 'required|string',
            'sku' => 'required',
            'barcode' => 'required',
            'price' => 'required',
            'cost' => 'required',
        ]);

        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
