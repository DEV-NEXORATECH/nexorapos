<?php
namespace App\Validations\Admin\User;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserStoreValidation{
    public function validate($inputs){
   $validator = Validator::make($inputs, [
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'profile_image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return $validator->errors()->first();
    }

    }

}
