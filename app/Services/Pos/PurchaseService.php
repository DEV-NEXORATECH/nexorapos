<?php
namespace App\Services\Pos;
use App\Models\Pos\Purchase;
use App\Models\Pos\PurchaseItem;
use App\Models\Pos\Product;
use App\Models\Pos\StockMovement;
use App\Models\Pos\Store;
use Illuminate\Http\Request;
use App\Models\Pos\Supplier;
use App\Pos\Purchase\PurchaseStoreValidation;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PurchaseService{
    public $purchaseStoreVal;
    public function __construct()
    {
        $this->purchaseStoreVal=new PurchaseStoreValidation();
    }
    public function index()
    {
        if (request()->ajax()) {
            $data = Purchase::with(['company']);
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('purchase_number', function($row) { return $row->purchase_number; })
                ->addColumn('date', function($row) { return $row->date; })
                ->addColumn('supplier', function($row) { return $row->supplier ? $row->supplier->name : "-"; })
                ->addColumn('reference', function($row) { return $row->reference_number; })
                ->addColumn('total_amount', function($row) { return "Rp " . number_format($row->total_amount); })
                ->addColumn('status', function($row) { return $row->status; })
                ->addColumn('company', function($row) { return $row->company ? $row->company->name : "-"; })
                ->addColumn('created_by', function($row) { return "-"; })
                ->addColumn('created_at', function($row) { return $row->created_at ? $row->created_at->format("d M Y H:i") : "-"; })
                ->addColumn('action', function($row) { return '<a href="'.url('admin-panel/purchase/edit/'.$row->id).'" class="nexora-action-btn is-warning"><i class="fas fa-edit"></i></a><a href="'.url('admin-panel/purchase/delete/'.$row->id).'" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Delete?\')"><i class="fas fa-trash"></i></a>'; })
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pos.purchase.listing');
    }

    public function add()
    {
        $companyId = auth()->user()?->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->get();
        $products = Product::where('company_id', $companyId)->get();
        $stores = Store::where('company_id', $companyId)->get();
        return view('admin.pos.purchase.add', compact('suppliers', 'products', 'stores'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
            $companyId = auth()->user()?->company_id;
            $inputs=$request->all();
            $errorMessage=$this->purchaseStoreVal->validate($inputs);
            if($errorMessage)
            {
                DB::rollBack();
                return redirect()->back()->with('failed', $errorMessage)->withInput();
            }
            if (! Supplier::whereKey($request->supplier_id)->where('company_id', $companyId)->exists()) {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Supplier tidak valid untuk company ini.')->withInput();
            }

            if (!$request->has('items') || empty($request->items)) {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Please add at least one item.')->withInput();
            }

            // Determine store_id
            $storeId = $request->store_id ?: Store::where('company_id', $companyId)->first()?->id;

            $purchase=new Purchase();
            $purchase->company_id = $companyId;
            $purchase->supplier_id=$request->supplier_id;
            $purchase->total_amount=$request->total_amount;
            $purchase->status=$request->status;
            $purchase->save();

            foreach ($request->items as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity'])) continue;

                if (! Product::whereKey($item['product_id'])->where('company_id', $companyId)->exists()) {
                    continue; // Skip invalid products
                }

                $purchaseItem = new PurchaseItem();
                $purchaseItem->company_id = $companyId;
                $purchaseItem->purchase_id = $purchase->id;
                $purchaseItem->product_id = $item['product_id'];
                $purchaseItem->quantity = $item['quantity'];
                $purchaseItem->cost = $item['cost'] ?? 0;
                $purchaseItem->save();

                if (strtoupper($purchase->status) === 'RECEIVED') {
                    // Increment stock
                    $product = Product::find($item['product_id']);
                    if ($product && $product->track_stock) {
                        $product->increment('stock_quantity', (int) $item['quantity']);
                    }

                    $movementPayload = [
                        'company_id' => $companyId,
                        'store_id' => $storeId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'type' => 'purchase',
                    ];
                    if (Schema::hasColumn('stock_movements', 'status')) {
                        $movementPayload['status'] = 'active';
                    }
                    StockMovement::create($movementPayload);
                }
            }

            DB::commit();
            return redirect()->to('admin-panel/purchase/listing')->with('success', "Record Added Successfully");
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $companyId = auth()->user()?->company_id;
        $purchase = Purchase::with('purchase_items.product', 'supplier')
            ->where('company_id', $companyId)
            ->find($id);

        if(!$purchase)
        {
            return redirect()->back()->with('failed', "Record Not Found or Unauthorized");
        }

        if (strtoupper($purchase->status) === 'RECEIVED') {
            return redirect()->back()->with('failed', 'Received purchases cannot be edited.');
        }

        $suppliers = Supplier::where('company_id', $companyId)->get();
        $products = Product::where('company_id', $companyId)->get();
        $stores = Store::where('company_id', $companyId)->get();
        return view('admin.pos.purchase.update', compact('purchase', 'suppliers', 'products', 'stores'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $companyId = auth()->user()?->company_id;
            $purchase = Purchase::where('company_id', $companyId)->find($id);
            if (! $purchase) {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
            }

            if (strtoupper($purchase->status) === 'RECEIVED') {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Received purchases cannot be edited.');
            }

            $inputs=$request->all();
            $errorMessage=$this->purchaseStoreVal->validate($inputs);
            if($errorMessage){
                DB::rollBack();
                return redirect()->back()->with('failed', $errorMessage);
            }
            if (! Supplier::whereKey($request->supplier_id)->where('company_id', $companyId)->exists()) {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Supplier tidak valid untuk company ini.')->withInput();
            }

            if (!$request->has('items') || empty($request->items)) {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Please add at least one item.')->withInput();
            }

            $storeId = $request->store_id ?: Store::where('company_id', $companyId)->first()?->id;

            $purchase->supplier_id=$request->supplier_id;
            $purchase->status=$request->status;
            $purchase->total_amount=$request->total_amount;
            $purchase->save();

            // Clear old items
            PurchaseItem::where('purchase_id', $purchase->id)->delete();

            foreach ($request->items as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity'])) continue;

                $purchaseItem = new PurchaseItem();
                $purchaseItem->company_id = $companyId;
                $purchaseItem->purchase_id = $purchase->id;
                $purchaseItem->product_id = $item['product_id'];
                $purchaseItem->quantity = $item['quantity'];
                $purchaseItem->cost = $item['cost'] ?? 0;
                $purchaseItem->save();

                if (strtoupper($purchase->status) === 'RECEIVED') {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->track_stock) {
                        $product->increment('stock_quantity', (int) $item['quantity']);
                    }

                    $movementPayload = [
                        'company_id' => $companyId,
                        'store_id' => $storeId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'type' => 'purchase',
                    ];
                    if (Schema::hasColumn('stock_movements', 'status')) {
                        $movementPayload['status'] = 'active';
                    }
                    StockMovement::create($movementPayload);
                }
            }

            DB::commit();
            return redirect()->to('admin-panel/purchase/listing')->with('success', 'Record Updated Successfully');
        }catch(\Exception $e)
        {
            DB::rollBack();
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function show($id)
    {
        $companyId = auth()->user()?->company_id;
        $purchase = Purchase::with(['supplier'])
            ->where('company_id', $companyId)
            ->find($id);
        if (!$purchase) {
            return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
        }
        return view('admin.pos.purchase.show', compact('purchase'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $companyId = auth()->user()?->company_id;
            $purchase = Purchase::with('purchase_items')
                ->where('company_id', $companyId)
                ->find($id);

            if(!$purchase)
            {
                DB::rollBack();
                return redirect()->back()->with('failed', "Record Not Found or Unauthorized");
            }

            $oldValue = $purchase->only(['id', 'status', 'total_amount']);
            $storeId = Store::where('company_id', $companyId)->first()?->id;

            // If it was RECEIVED, we must reverse the stock
            if (strtoupper($purchase->status) === 'RECEIVED') {
                foreach ($purchase->purchase_items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product && $product->track_stock) {
                        $product->decrement('stock_quantity', (int) $item->quantity);
                    }

                    $movementPayload = [
                        'company_id' => $companyId,
                        'store_id' => $storeId,
                        'product_id' => $item->product_id,
                        'quantity' => (string) (-1 * (int) $item->quantity),
                        'type' => 'purchase_cancel',
                    ];
                    if (Schema::hasColumn('stock_movements', 'status')) {
                        $movementPayload['status'] = 'active';
                    }
                    StockMovement::create($movementPayload);
                }
            }

            $purchase->status = 'cancelled';
            if (Schema::hasColumn('purchases', 'cancelled_at')) {
                $purchase->cancelled_at = now();
            }
            $purchase->save();
            AuditLogger::log('cancel', 'purchases', $purchase, $oldValue, $purchase->only(['id', 'status', 'total_amount']));
            DB::commit();
            return redirect()->back()->with('success', 'Purchase cancelled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
