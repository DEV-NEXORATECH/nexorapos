<?php

namespace app\Services\Front;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Contact;
class HomeService
{
    public function index()
    {
        $posts=Post::with('categories')->get();

        return view('front.home', compact('posts'));
    }

    public function archive()
    {
        return view('front.archive');
    }

    public function category()
    {
        return view('front.category');
    }

    public function blogDetail()
    {
        return view('front.blog_detail');
    }

    public function contact()
    {
        return view('front.contact');
    }

    public function  store(Request $request)
    {
        try{
            $contact=new Contact();
            $contact->name=$request->name;
            $contact->email=$request->email;
            $contact->subject=$request->subject;
            $contact->message=$request->message;
            $contact->save();
            if($contact){
                return redirect()->back()->with('success', 'We Have Received Your Message, Contact You Soon!');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage);
        }
    }
}
