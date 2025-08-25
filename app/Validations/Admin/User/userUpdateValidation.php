<?php
namespace App\Validations\Admin\User;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserUpdateValidation{
    public function validate($inputs,$id){
        $validator=Validator::make($inputs, [
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email,' .$id,
        'profile_image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
