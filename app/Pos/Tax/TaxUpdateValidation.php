<?php
namespace App\Pos\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Pos\Tax;
class TaxUpdateValidation{

    public function validate($inputs,$id)
    {
        $validator=Validator::make($inputs, [
            'name' => 'required|string|max:255|unique:taxes,name,'.$id,
             'rate' => 'required|numeric',
        ]);

        if($validator->fails())
        {
            return $validator->errors()->first();
        }
    }
}
