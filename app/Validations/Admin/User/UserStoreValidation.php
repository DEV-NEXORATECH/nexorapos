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
        'phone'    => 'required|string|max:30',
        'role_id'  => 'required|exists:roles,id',
        'company_id' => (auth()->user()?->isSuperAdmin() ? 'required' : 'nullable').'|exists:companies,id',
        'is_super_admin' => 'nullable|boolean',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8',
        'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return $validator->errors()->first();
    }

    }

}
