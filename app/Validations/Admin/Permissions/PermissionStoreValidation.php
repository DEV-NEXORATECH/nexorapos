<?php
namespace App\Validations\Admin\Permissions;
use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermissionStoreValidation{
    public function validate($inputs){
        $validator=Validator::make($inputs,[
            'permission_category_id' => 'required',
            'name'  => 'required|string|max:255|unique:permissions,name' ,
        'route' => 'required|string|max:255',

        ]);

    if ($validator->fails()) {
        return $validator->errors()->first();
    }
    }
}
