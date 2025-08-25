<?php
namespace App\Validations\Admin\RU;
use Illuminate\Http\Request;
use App\Models\RoleUser;
use Illuminate\Support\Facades\Validator;
class RoleUserStore{

    public function validate($inputs)
    {
        $validator=Validator::make($inputs, [
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|array',
            'role_id.*' => 'exists:roles,id',
        ]);

        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
