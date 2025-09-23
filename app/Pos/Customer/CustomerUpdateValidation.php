<?php
namespace App\Pos\Customer;

use Illuminate\Support\Facades\Validator;

class CustomerUpdateValidation
{
    public function validate($inputs, $id)
    {
        $validator = Validator::make($inputs, [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:customers,email,'.$id,
            'phone'   => 'required|min:11|max:15',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return true; // valid
    }
}
