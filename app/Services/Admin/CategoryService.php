<?php
namespace App\Services\Admin;

use Illuminate\Http\Request;
use App\Validations\Admin\Category\CategoryStoreValidation;
use App\Validations\Admin\Category\CategoryUpdateValidation;
use App\Models\Category;

class CategoryService{

    public $categoryStoreValidation;
    public $categoryUpdateValidation;

    public function __construct()
    {
        $this->categoryStoreValidation=new CategoryStoreValidation();
        $this->categoryUpdateValidation=new CategoryUpdateValidation();

    }

    public function category()
    {
        return view('admin.category.add');
    }


  public function index()
    {
        // Retrieve categories from the database with pagination (5 records per page)
        $categories = Category::all();

        // Return the 'listing' view inside 'admin/category' folder
        // Pass the retrieved categories to the view using compact()
        return view('admin.category.listing', compact('categories'));
    }

    public function store(Request $request)
    {
        // Get all form input data from the request
        $inputs = $request->all();

        // Validate the input data using categoryStoreValidation service/class
        // If validation fails, it will return an error message
        $errorMessage = $this->categoryStoreValidation->validate($inputs);

        // If there is an error message, redirect back with the error status
        if ($errorMessage) {
            return redirect()->back()->with('failed', $errorMessage);
        }

        // Create a new Category model instance
        $category = new Category();

        // Assign form data to category fields
        $category->name = $request->name;
        $category->description = $request->description;

        // Save the category to the database and store the result (true/false)
        $saved = $category->save();

        // If save was successful, redirect to the categories listing page with a success message
        if ($saved) {
            return redirect('admin-panel/categories/listing')->with('success', 'Category Added Successfully');
        }
        // If save failed, redirect back with a failure message
        else {
            return back()->with('failed', 'Something Went wrong');
        }
    }

   public function edit(Request $request, $id)
    {
        // Find the category by its ID
        $category = Category::find($id);

        // Return the 'update' view inside 'admin/category' folder
        // Pass the retrieved category data to the view using compact()
        return view('admin.category.update', compact('category'));
    }


    public function update(Request $request, $id)
    {
        // Find the category record by its ID
        $category = Category::find($id);

        // Get all form input data from the request
        $inputs = $request->all();

        // Validate the input data using categoryUpdateValidation service/class
        // If validation fails, it returns an error message
        $errorMessage = $this->categoryUpdateValidation->validate($inputs);

        // If there is a validation error, redirect back with the error message
        if ($errorMessage) {
            return redirect()->back()->with('failed', $errorMessage);
        }

        // Assign updated values to the category fields
        $category->name = $request->name;
        $category->description = $request->description;

        // Save the updated category to the database and store the result (true/false)
        $saved = $category->save();

        // If the update was successful, redirect to categories listing with a success message
        if ($saved) {
            return redirect('admin-panel/categories/listing')->with('success', 'Category Updated Successfully');
        }
        // If the update failed, redirect back with a failure message
        else {
            return back()->with('failed', 'Something Went wrong');
        }
    }

    public function destroy(Request $request, $id)
    {
        // Find the category record by its ID
        $categories = Category::find($id);

        // Delete the found category from the database
        $categories->delete();

        // If deletion was successful, redirect to categories listing with a success message
        if ($categories) {
            return redirect('admin-panel/categories/listing')->with('success', 'Category Deleted Successfully');
        }
        // If deletion failed, redirect back with a failure message
        else {
            return back()->with('failed', 'Something Went wrong');
        }
    }

}
