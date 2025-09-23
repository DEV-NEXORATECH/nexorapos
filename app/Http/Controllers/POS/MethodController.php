<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\POS\Method;
class MethodController extends Controller
{
   public function index()
    {
        $methods=Method::all();

        return view('admin.pos.method.listing', compact('methods'));
    }

    public function add()
    {
        return view('admin.pos.method.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $method=new Method();
        $method->name=$request->name;
        $method->save();
        if($method){
            return redirect()->to('admin-panel/payment-methods/listing')->with('success', 'Methode Added Successfully');
        }
        return redirect()->back()->with('failed', "Something Went Wrong");
    }

    public function edit($id){
        $method=Method::find($id);
        if($method){
            return view('admin.pos.method.update', compact('method'));
        }

        return redirect()->back()->with('failed', 'Record Not Found');
    }

    public function update(Request $request,$id)
    {
        $method=Method::find($id);
        if($method){
            $method->name=$request->name;
            $method->save();
            return redirect()->to('admin-panel/payment-methods/listing')->with('success', 'Record Updated Successfully');
        }

        return redirect()->back()->with('failed', 'Something Went Wrong');
    }

    public function destroy($id)
    {
        $method=Method::find($id);
        if($method){
            $method->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        return redirect()->bacK()->with('failed', 'Record Not Found');
    }
}
