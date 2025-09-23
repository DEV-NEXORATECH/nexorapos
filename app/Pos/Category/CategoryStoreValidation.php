<?php
namespace App\Pos\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Pos\ProductCategory;
class CategoryStoreValidation{
    public function validate($inputs)
    {
        $validator=Validator::make($inputs,[
            'name'=>'required|unique:product_categories,name'
        ]);
        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
