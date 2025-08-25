<?php
namespace App\Validations\Admin\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class CategoryStoreValidation{
    public function validate($inputs)
    {
        $validator=Validator::make($inputs, [
             'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        if($validator->fails())
        {
            return $validator->errors()->first();
        }
    }
}
