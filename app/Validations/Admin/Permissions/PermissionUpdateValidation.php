<?php

namespace App\Validations\Admin\Permissions;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request;
use App\Models\Category;

class PermissionUpdateValidation{
    public function validate($inputs,$id){
        $validator=Validator::make($inputs,[
        'permission_category_id' => 'required',
        'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        'route' => 'required|string|max:255|unique:permissions,route,' . $id,


        ]);

    if ($validator->fails()) {
        return $validator->errors()->first();
    }
    }
}
