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
        'phone'    => 'required|string|max:30',
        'role_id'  => 'required|exists:roles,id',
        'company_id' => (auth()->user()?->isSuperAdmin() ? 'required' : 'nullable').'|exists:companies,id',
        'password' => 'nullable|string|min:8|confirmed',
        'password_confirmation' => 'nullable|string|min:8',
        'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
