<?php
namespace App\Validations\Admin\Roles;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
class RoleUpdateValidation{
    public function validate($inputs){

               $validator=Validator::make($inputs, [
            'role' => 'required|string|max:50',
        ]);

        if($validator->fails()){
            return $validator->errors()->first();
        }

    }
}
