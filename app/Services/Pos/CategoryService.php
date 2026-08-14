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
        if (request()->ajax()) {
            $data = ProductCategory::with(['company']);
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('id', function($row) { return $row->id; })
                ->addColumn('name', function($row) { return $row->name; })
                ->addColumn('company', function($row) { return $row->company ? $row->company->name : "-"; })
                ->addColumn('created_by', function($row) { return "-"; })
                ->addColumn('created_at', function($row) { return $row->created_at ? $row->created_at->format("d M Y H:i") : "-"; })
                ->addColumn('action', function($row) { return '<a href="'.url('admin-panel/categories/edit/'.$row->id).'" class="nexora-action-btn is-warning"><i class="fas fa-edit"></i></a><a href="'.url('admin-panel/categories/delete/'.$row->id).'" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Delete?\')"><i class="fas fa-trash"></i></a>'; })
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pos.categories.listing');
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
            $category->company_id = (auth()->user()?->isSuperAdmin() && $request->company_id) ? $request->company_id : auth()->user()?->company_id;
            $category->name = $request->name;
            $category->save();
            return redirect('admin-panel/categories/listing')->with('success', 'Category added successfully.');
        }catch(\Exception $e){
            return redirect()->back()->withErrors(['failed' => 'An error occurred while adding the category. Please try again.'])->withInput();
        }

    }

    public function edit(Request $request, $id)
    {
        $category = ProductCategory::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if (!$category) {
            return redirect('admin-panel/categories/listing')->withErrors(['failed' => 'Category not found.']);
        }
        return view('admin.pos.categories.update', compact('category'));
    }

    public function update(Request $request, $id)
    {
        try{
            $category = ProductCategory::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
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
            $category = ProductCategory::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
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
