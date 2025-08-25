<?php
namespace App\Validations\Admin\Post;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;


class PostUpdateValidation{

    public function validator($inputs){
        $validator=Validator::make($inputs,[
           'user_id' => 'required',
            'category_id' => 'required',
            'title' => 'required|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'content' => 'required',
        ]);

        if($validator->fails()){
            return $validator->errors()->first();
        }
    }
}
