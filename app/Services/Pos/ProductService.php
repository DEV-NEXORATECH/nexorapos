<?php
namespace App\Services\Pos;
use Illuminate\Http\Request;
use App\Models\Pos\Product;
use App\Models\Pos\Tax;
use App\Models\Pos\ProductCategory;
use App\Pos\Product\ProductStoreValidation;
use App\Pos\Product\ProductUpdateValidation;
class ProductService
{
    public $productStoreValidation;
    public $productUpdateValidation;
    public function __construct(){
        $this->productStoreValidation=new ProductStoreValidation();
        $this->productUpdateValidation=new ProductUpdateValidation();
    }
    public function index(){
        $products=Product::with('tax')->get();
        return view('admin.pos.Product.listing', compact('products'));
    }

    public function add()
    {
        $taxes=Tax::all();
        $categories=ProductCategory::all();
        return view('admin.pos.Product.add', compact('taxes', 'categories'));
    }

    public function store(Request $request)
    {
        try{
             $inputs=$request->all();
            $validator=$this->productStoreValidation->validate($inputs);
            if($validator){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $product=new Product();
        $product->name=$request->name;
        $product->tax_id=$request->tax_id;
        $product->category_id=$request->category_id;
        $product->sku=$request->sku;
        $product->barcode=$request->barcode;
        $product->price=$request->price;
        $product->cost=$request->cost;
        $product->track_stock=$request->track_stock;
        $product->save();
        return redirect()->to('admin-panel/products/listing')->with('success','Product added successfully');
    }catch(\Exception $e){
        return redirect()->back()->with('failed',$e->getMessage())->withInput();
    }

    }

    //edit product

    public function edit(Request $request, $id)
    {
        $product = Product::with('tax', 'category')->find($id);
        $taxes = Tax::all();
        $categories = ProductCategory::all();
        return view('admin.pos.Product.update', compact('product', 'taxes', 'categories'));
    }

    //update function

    public function update(Request $request,$id)
    {
        try{
            $inputs=$request->all();
            $validator=$this->productUpdateValidation->validate($inputs,$id);
            if($validator){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $product=Product::find($id);
            $product->name=$request->name;
            $product->tax_id=$request->tax_id;
            $product->category_id=$request->category_id;
            $product->sku=$request->sku;
            $product->barcode=$request->barcode;
            $product->price=$request->price;
            $product->cost=$request->cost;
            $product->track_stock=$request->track_stock;
            $product->save();
            return redirect()->to('admin-panel/products/listing')->with('success','Product updated successfully');
        }catch(\Exception $e){
            return redirect()->back()->with('failed',$e->getMessage())->withInput();
        }
    }

    //delete function

    public function destroy($id)
    {
        try{
            $product=Product::find($id);
            if(!$product){
                return redirect()->back()->with('failed','Product not found');
            }
            $product->delete();
            return redirect()->to('admin-panel/products/listing')->with('success','Product deleted successfully');
        }catch(\Exception $e){
            return redirect()->back()->with('failed',$e->getMessage());
        }
    }

    // Soft deleted products listing
    public function deletedProducts()
    {
        $deletedProducts = Product::onlyTrashed()->with('tax')->get();
        return view('admin.pos.Product.archieve', compact('deletedProducts'));
    }

    public function restore($id)
    {
        $product=Product::withTrashed()->find($id);
        if(!$product)
        {
            return redirect()->back()->with('failed', 'Product Not Found');
        }
        $product->restore();
        return redirect()->to('admin-panel/products/listing')->with('success', 'Product Restored Successfully');
    }

    public function forceDelete($id)
    {
        $product=Product::find($id);
        $product->forceDelete();
        return redirect()->back()->with('success', 'Your Record Deleted Permanently');
    }

    // Export Products to Excel File 
    public function export(){
        try{
            $fileName = 'products.xlsx';
            $products = Product::with('tax', 'category')->get();
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            // Set header row
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Name');
            $sheet->setCellValue('C1', 'SKU');
            $sheet->setCellValue('D1', 'Barcode');
            $sheet->setCellValue('E1', 'Price');
            $sheet->setCellValue('F1', 'Cost');
            $sheet->setCellValue('G1', 'Tax');
            $sheet->setCellValue('H1', 'Category');
            $sheet->setCellValue('I1', 'Track Stock');
            // Populate data rows
            $row = 2;
            foreach ($products as $product) {
                $sheet->setCellValue('A' . $row, $product->id);
                $sheet->setCellValue('B' . $row, $product->name);
                $sheet->setCellValue('C' . $row, $product->sku);
                $sheet->setCellValue('D' . $row, $product->barcode);
                $sheet->setCellValue('E' . $row, $product->price);
                $sheet->setCellValue('F' . $row, $product->cost);
                $sheet->setCellValue('G' . $row, $product->tax ? $product->tax->name : '');
                $sheet->setCellValue('H' . $row, $product->category ? $product->category->name : '');
                $sheet->setCellValue('I' . $row, $product->track_stock ? 'Yes' : 'No');
                $row++;
            }
            // Write to file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            // Save the file to a temporary location
            $tempFilePath = sys_get_temp_dir() . '/' . $fileName;
            $writer->save($tempFilePath);
            // Return the file as a download response
            return response()->download($tempFilePath, $fileName)->deleteFileAfterSend(true);
        }catch(\Exception $e){
            return redirect()->back()->with('failed',$e->getMessage());
    }
}


    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // max 10MB
        ]);

        // Get the uploaded file
        $file = $request->file('file');

        // Optional: Get original filename
        $filename = $file->getClientOriginalName();

        // Optional: Store file temporarily
        $path = $file->storeAs('uploads', $filename);

        // Example: Process file (using Laravel Excel or custom logic)
        // Excel::import(new ProductsImport, $file);

        // Return JSON response
        return response()->json([
            'message' => 'File imported successfully',
            'file_name' => $filename,
            'path' => $path
        ]);
    }
}


