<?php
namespace App\Validations\Admin\Setting;
use Illuminate\Support\Facades\Validator;

class SettingStoreValidation {
    public function validate($inputs){
        foreach($inputs as $key => $value) {
            // Skip empty values
            if ($value === null || $value === '') continue;

            $validator = Validator::make(
                ['key' => $key, 'value' => $value],
                [
                    'key' => 'required|string',
                    'value' => 'required|string',
                ]
            );

            if($validator->fails()) {
                return $validator->errors()->first();
            }
        }
    }
}
