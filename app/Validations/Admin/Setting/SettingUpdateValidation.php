<?php

namespace App\Validations\Admin\Setting;

use Illuminate\Support\Facades\Validator;

class SettingUpdateValidation
{
    public function validate($inputs, $id)
    {
        $validator = Validator::make($inputs, [
            'key' => 'required|string|max:255|unique:settings,key,' . $id,
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }


    }
}
