<?php

namespace App\Services\Pos;

use Illuminate\Http\Request;
use App\Models\POS\PurchaseItem;
use App\Models\POS\Purchase;
use App\Models\POS\Product;
use App\Models\POS\Supplier;
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
        $items = PurchaseItem::with('product', 'purchase')->get();
        return view('admin.pos.item.listing', compact('items'));
    }

    // Show the form for creating a new purchase item
    public function add()
    {
        $purchases = Purchase::all(); // Fetch all purchases
        $products = Product::all();   // Fetch all products
        return view('admin.pos.item.add', compact('purchases', 'products'));
    }

    // Store a newly created purchase item
    public function store(Request $request)
    {
        try {
            $inputs = $request->all(); // Retrieve all request inputs

            // Validate inputs using custom validation logic
            $errorMessage = $this->item->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }

            // Create and save the new purchase item
            $item = new PurchaseItem();
            $item->purchase_id = $request->purchase_id;
            $item->product_id = $request->product_id;
            $item->quantity = $request->quantity;
            $item->cost = $request->cost;
            $item->save();

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
        $products = Product::all();    // Fetch all products
        $purchases = Purchase::all();  // Fetch all purchases

        // Find the item by ID with related product and purchase info
        $item = PurchaseItem::with('product', 'purchase')->find($id);

        // If item exists, load the edit view
        if ($item) {
            return view('admin.pos.item.update', compact('item', 'products', 'purchases'));
        }

        // If item not found, redirect back with error
        return redirect()->back()->with('failed', 'Record Not Found');
    }

    // Update the specified purchase item
    public function update(Request $request, $id)
    {
        $item = PurchaseItem::find($id); // Find the item to update

        try {
            $inputs = $request->all(); // Retrieve all request data

            // Validate inputs using custom logic
            $errorMessage = $this->item->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }

            // Update item fields
            $item->purchase_id = $request->purchase_id;
            $item->product_id = $request->product_id;
            $item->quantity = $request->quantity;
            $item->cost = $request->cost;
            $item->save();

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
        $item = PurchaseItem::find($id); // Find the item by ID

        // If item exists, delete it
        if ($item) {
            $item->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }

        // If item not found, return with error
        return redirect()->back()->with('failed', 'Record Not Found');
    }
}
