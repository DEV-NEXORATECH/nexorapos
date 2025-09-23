<?php
namespace App\Services\Pos;
use App\Models\Pos\ProductCategory;
use Illuminate\Http\Request;
use App\Pos\Category\CategoryStoreValidation;
use App\Pos\Category\CategoryUpdateValidation;

class CategoryService{
    public $categoryStoreValidation;
    public $categoryUpdateValidation;
    public function __construct(){
        $this->categoryStoreValidation = new CategoryStoreValidation();
        $this->categoryUpdateValidation = new CategoryUpdateValidation();
    }
    public function index(){
        $categories = ProductCategory::all();
        return view('admin.pos.categories.listing', compact('categories'));
    }

    public function add(){
        return view('admin.pos.categories.add');
    }

    public function store(Request $request)
    {
        try{
            $inputs = $request->all();
            $validationError = $this->categoryStoreValidation->validate($inputs);
            if ($validationError) {
            return redirect()->back()->withErrors(['failed' => $validationError])->withInput();
            }
            $category = new ProductCategory();
            $category->name = $request->name;
            $category->save();
            return redirect('admin-panel/categories/listing')->with('success', 'Category added successfully.');
        }catch(\Exception $e){
            return redirect()->back()->withErrors(['failed' => 'An error occurred while adding the category. Please try again.'])->withInput();
        }

    }

    public function edit(Request $request, $id)
    {
        $category = ProductCategory::find($id);
        if (!$category) {
            return redirect('admin-panel/categories/listing')->withErrors(['failed' => 'Category not found.']);
        }
        return view('admin.pos.categories.update', compact('category'));
    }

    public function update(Request $request, $id)
    {
        try{
            $category = ProductCategory::find($id);
            if (!$category) {
                return redirect('admin-panel/categories/listing')->withErrors(['failed' => 'Category not found.']);
            }
            $inputs = $request->all();

            $validationError = $this->categoryUpdateValidation->validate($inputs,$id);
            if ($validationError) {
            return redirect()->back()->withErrors(['failed' => $validationError])->withInput();
            }
            $category->name = $request->name;
            $category->save();
            return redirect('admin-panel/categories/listing')->with('success', 'Category updated successfully.');
        }catch(\Exception $e){
            return redirect()->back()->withErrors(['failed' => 'An error occurred while updating the category. Please try again.'])->withInput();
        }
    }

    public function destroy($id)
    {
        try{
            $category = ProductCategory::find($id);
            if (!$category) {
                return redirect('admin-panel/categories/listing')->withErrors(['failed' => 'Category not found.']);
            }
            $category->delete();
            return redirect('admin-panel/categories/listing')->with('success', 'Category deleted successfully.');
        }catch(\Exception $e){
            return redirect()->back()->withErrors(['failed' => 'An error occurred while deleting the category. Please try again.']);
        }
    }
}
