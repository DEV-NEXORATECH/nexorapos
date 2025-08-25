<?php
namespace App\Services\Admin;
use Illuminate\Http\Request;
use App\Models\PermissionCategory;
use App\Validations\Admin\PC\PcStoreValidation;

class PermissionCategoryService{

    public $pcStoreValidation;
    public function __construct()
    {
         $this->pcStoreValidation=new PcStoreValidation();
    }

    public function index()
    {
        $categories=PermissionCategory::all();
        return view('admin.PC.listing', compact('categories'));
    }

    public function add()
    {
        return view('admin.PC.add');
    }

    public function store(Request $request)
    {
        try{
            $inputs=$request->aLL();
            $errorMessage=$this->pcStoreValidation->validate($inputs);
            if($errorMessage)
            {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $category=new PermissionCategory();
            $category->display_order=$request->display_order;
            $category->name=$request->name;
            $category->icons=$request->icons;
            $category->save();
            if($category)
            {
                return redirect()->to('admin-panel/permission-categories/listing')->with('success', 'Category Added Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function edit(Request $request,$id)
    {
        $category=PermissionCategory::find($id);
        if($category){
            return view('admin.PC.update', compact('category'));
        }
    }

    public function update(Request $request,$id)
    {
        try{
            $category=PermissionCategory::find($id);
            $inputs=$request->aLL();
            $errorMessage=$this->pcStoreValidation->validate($inputs);
            if($errorMessage)
            {
                return redirect()->back()->with('failed', $errorMessage);
            }
            $category->display_order=$request->display_order;
            $category->name=$request->name;
            $category->icons=$request->icons;
            $category->save();
            if($category)
            {
                return redirect()->to('admin-panel/permission-categories/listing')->with('success', 'Category Updated Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    //delete function

    public function destroy(Request $request,$id)
    {
        try{
            $category=PermissionCategory::find($id);
            $category->delete();
            if($category)
            {
                 return redirect()->to('admin-panel/permission-categories/listing')->with('success', 'Category Deleted Successfully');

            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
