<?php
namespace App\Pos\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Pos\ProductCategory;
class CategoryUpdateValidation{
    public function validate($inputs,$id)
    {
        $validator=Validator::make($inputs,[
            'name'=>'required|unique:product_categories,name,'.$id
        ]);
        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
