<?php
namespace App\Pos\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Pos\Tax;
class TaxStoreValidation{

    public function validate($inputs)
    {
        $validator=Validator::make($inputs, [
            'name' => 'required|string|max:255',
             'rate' => 'required|numeric',
        ]);

        if($validator->fails())
        {
            return $validator->errors()->first();
        }
    }
}
