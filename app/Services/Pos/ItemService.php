<?php

namespace App\Services\Pos;

use Illuminate\Http\Request;
use App\Models\Pos\PurchaseItem;
use App\Models\Pos\Purchase;
use App\Models\Pos\Product;
use App\Models\Pos\Supplier;
use App\Pos\Item\ItemStoreValidation;

class ItemService
{
    public $item;

    public function __construct()
    {
        // Initialize the ItemStoreValidation class for custom validation
        $this->item = new ItemStoreValidation();
    }

    // Display a list of all purchase items along with their related product and purchase info
    public function index()
    {
        $companyId = auth()->user()?->company_id;
        $items = PurchaseItem::with('product', 'purchase')
            ->where('company_id', $companyId)
            ->get();
        return view('admin.pos.item.listing', compact('items'));
    }

    // Show the form for creating a new purchase item
    public function add()
    {
        $companyId = auth()->user()?->company_id;
        $purchases = Purchase::where('company_id', $companyId)->get(); // Fetch all purchases
        $products = Product::where('company_id', $companyId)->get();   // Fetch all products
        return view('admin.pos.item.add', compact('purchases', 'products'));
    }

    // Store a newly created purchase item
    public function store(Request $request)
    {
        try {
            $companyId = auth()->user()?->company_id;
            $inputs = $request->all(); // Retrieve all request inputs

            // Validate inputs using custom validation logic
            $errorMessage = $this->item->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }
            if (! Purchase::whereKey($request->purchase_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Purchase tidak valid untuk company ini.')->withInput();
            }
            if (! Product::whereKey($request->product_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Product tidak valid untuk company ini.')->withInput();
            }

            // Create and save the new purchase item
            $item = new PurchaseItem();
            $item->company_id = $companyId;
            $item->purchase_id = $request->purchase_id;
            $item->product_id = $request->product_id;
            $item->quantity = $request->quantity;
            $item->cost = $request->cost;
            $item->save();

            $this->adjustStock($item->product_id, (int) $item->quantity);

            // Redirect on success
            if ($item) {
                return redirect()->to('admin-panel/purchase-items/listing')->with('success', 'Record Added Successfully');
            }
        } catch (\Exception $e) {
            // Handle any exceptions and return with an error message
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    // Show the form for editing a specific purchase item
    public function edit($id)
    {
        $companyId = auth()->user()?->company_id;
        $products = Product::where('company_id', $companyId)->get();    // Fetch all products
        $purchases = Purchase::where('company_id', $companyId)->get();  // Fetch all purchases

        // Find the item by ID with related product and purchase info
        $item = PurchaseItem::with('product', 'purchase')
            ->where('company_id', $companyId)
            ->find($id);

        // If item exists, load the edit view
        if ($item) {
            return view('admin.pos.item.update', compact('item', 'products', 'purchases'));
        }

        // If item not found, redirect back with error
        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    // Update the specified purchase item
    public function update(Request $request, $id)
    {
        $companyId = auth()->user()?->company_id;
        $item = PurchaseItem::where('company_id', $companyId)->find($id); // Find the item to update

        try {
            $inputs = $request->all(); // Retrieve all request data

            // Validate inputs using custom logic
            $errorMessage = $this->item->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }
            if (! $item) {
                return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
            }
            if (! Purchase::whereKey($request->purchase_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Purchase tidak valid untuk company ini.')->withInput();
            }
            if (! Product::whereKey($request->product_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Product tidak valid untuk company ini.')->withInput();
            }

            $oldProductId = $item->product_id;
            $oldQuantity = (int) $item->quantity;

            // Update item fields
            $item->purchase_id = $request->purchase_id;
            $item->product_id = $request->product_id;
            $item->quantity = $request->quantity;
            $item->cost = $request->cost;
            $item->save();

            if ((int) $item->product_id === $oldProductId) {
                $this->adjustStock($item->product_id, (int) $item->quantity - $oldQuantity);
            } else {
                $this->adjustStock($oldProductId, -$oldQuantity);
                $this->adjustStock($item->product_id, (int) $item->quantity);
            }

            // Redirect on success
            if ($item) {
                return redirect()->to('admin-panel/purchase-items/listing')->with('success', 'Record Updated Successfully');
            }
        } catch (\Exception $e) {
            // Handle exception during update
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    // Delete the specified purchase item
    public function destroy($id)
    {
        $companyId = auth()->user()?->company_id;
        $item = PurchaseItem::where('company_id', $companyId)->find($id); // Find the item by ID

        // If item exists, delete it
        if ($item) {
            $this->adjustStock($item->product_id, -(int) $item->quantity);
            $item->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        // If item not found, return with error
        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    // Adjust product stock (only when track_stock is enabled)
    private function adjustStock($productId, $delta)
    {
        $product = Product::find($productId);
        if (! $product || ! $product->track_stock) {
            return;
        }
        $newQuantity = max(0, (int) $product->stock_quantity + (int) $delta);
        $product->stock_quantity = $newQuantity;
        $product->save();
    }
}
