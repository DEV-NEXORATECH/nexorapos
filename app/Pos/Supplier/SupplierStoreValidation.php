<?php
namespace App\Pos\Supplier;

use Illuminate\Support\Facades\Validator;

class SupplierStoreValidation
{
    public function validate($inputs)
    {
        $validator = Validator::make($inputs, [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:suppliers,email',
            'phone'   => 'required|min:11|max:15',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return true; // valid
    }
}
