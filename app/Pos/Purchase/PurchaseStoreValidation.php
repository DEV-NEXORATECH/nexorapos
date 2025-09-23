<?php
namespace App\Pos\Purchase;
use App\Models\Pos\Purchase;
use Illuminate\Http\Request;
use App\Models\Pos\Supplier;
use Illuminate\Support\Facades\Validator;

class PurchaseStoreValidation{
    public function validate($inputs){
        $validator=Validator::make($inputs,[
            'supplier_id' =>'required',
            'total_amount' => 'required',
            'status'=> 'nullable|string|max:25'
        ]);

        if($validator->fails())
        {
            return $validator->errors()->first();
        }
    }
}
