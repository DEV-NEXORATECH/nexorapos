<?php
namespace App\Validations\Admin\RP;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RolePermission;

class RolePermissionStore{
    public function validate($inputs)
    {
        $validator=Validator::make($inputs, [
        'role_id' => 'required|exists:roles,id',
        'permissions' => 'required|array',
        'permissions.*' => 'exists:permissions,id',
        ]);

        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
