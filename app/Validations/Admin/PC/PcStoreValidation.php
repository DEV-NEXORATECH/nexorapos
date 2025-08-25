<?php
namespace App\Validations\Admin\PC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class PcStoreValidation{

    public function validate($inputs)
    {
        $validator=Validator::make($inputs, [
            'display_order' => 'required',
             'name' => 'required|string|max:255',
             'icons' => 'required|string|max:255',
        ]);

        if($validator->fails())
        {
            return $validator->errors()->first();
        }
    }
}
