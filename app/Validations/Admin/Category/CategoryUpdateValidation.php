<?php

namespace App\Validations\Admin\Category;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request;
use App\Models\Category;

class CategoryUpdateValidation{

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
