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

class PurchaseService
{
    public $purchaseStoreVal;

    public function __construct()
    {
        $this->purchaseStoreVal = new PurchaseStoreValidation();
    }

    public function index()
    {
        if (request()->ajax()) {
            $data = Purchase::with(['company', 'supplier'])
                ->when(!auth()->user()?->isSuperAdmin(), function ($q) {
                    return $q->where('company_id', auth()->user()?->company_id);
                });

            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('purchase_number', function($row) { 
                    return $row->purchase_number ?: 'PUR-'.$row->id; 
                })
                ->addColumn('date', function($row) { 
                    return $row->date ?: ($row->created_at ? $row->created_at->format('d M Y') : '-'); 
                })
                ->addColumn('supplier', function($row) { 
                    return $row->supplier ? $row->supplier->name : "-"; 
                })
                ->addColumn('reference', function($row) { 
                    return $row->reference_number ?: '-'; 
                })
                ->addColumn('total_amount', function($row) { 
                    return "Rp " . number_format($row->total_amount, 0, ',', '.'); 
                })
                ->addColumn('status', function($row) { 
                    return $row->status ?: 'PENDING'; 
                })
                ->addColumn('company', function($row) { 
                    return $row->company ? $row->company->name : "-"; 
                })
                ->addColumn('created_by', function($row) { 
                    return "-"; 
                })
                ->addColumn('created_at', function($row) { 
                    return $row->created_at ? $row->created_at->format("d M Y H:i") : "-"; 
                })
                ->addColumn('action', function($row) { 
                    $detailBtn = '<a href="'.url('admin-panel/purchase/show/'.$row->id).'" class="nexora-action-btn is-info" title="Lihat Rincian"><i class="fas fa-eye"></i></a>';
                    $editBtn = strtoupper($row->status) !== 'RECEIVED' && strtoupper($row->status) !== 'CANCELLED'
                        ? '<a href="'.url('admin-panel/purchase/edit/'.$row->id).'" class="nexora-action-btn is-warning" title="Edit Pembelian"><i class="fas fa-edit"></i></a>'
                        : '';
                    $deleteBtn = strtoupper($row->status) !== 'CANCELLED'
                        ? '<a href="'.url('admin-panel/purchase/delete/'.$row->id).'" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Batalkan pembelian ini?\')" title="Batalkan Pembelian"><i class="fas fa-trash"></i></a>'
                        : '';
                    return '<div class="nexora-action-group" style="display:flex; gap:6px; justify-content:center; flex-wrap:nowrap;">'
                        . $detailBtn . $editBtn . $deleteBtn
                        . '</div>'; 
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.pos.purchase.listing');
    }

    public function add()
    {
        $companyId = auth()->user()?->isSuperAdmin() ? (request('company_id') ?: auth()->user()?->company_id) : auth()->user()?->company_id;
        $suppliers = Supplier::when($companyId, fn($q)=>$q->where('company_id', $companyId))->orderBy('name')->get();
        $products = Product::when($companyId, fn($q)=>$q->where('company_id', $companyId))->orderBy('name')->get();
        $stores = Store::when($companyId, fn($q)=>$q->where('company_id', $companyId))->orderBy('name')->get();
        return view('admin.pos.purchase.add', compact('suppliers', 'products', 'stores'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $companyId = (auth()->user()?->isSuperAdmin() && $request->filled('company_id')) 
                ? $request->company_id 
                : auth()->user()?->company_id;

            $inputs = $request->all();
            $errorMessage = $this->purchaseStoreVal->validate($inputs);
            if ($errorMessage) {
                DB::rollBack();
                return redirect()->back()->with('failed', $errorMessage)->withInput();
            }

            if (!Supplier::whereKey($request->supplier_id)->when($companyId, fn($q)=>$q->where('company_id', $companyId))->exists()) {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Supplier tidak valid untuk perusahaan ini.')->withInput();
            }

            if (!$request->has('items') || empty($request->items)) {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Harap masukkan minimal 1 barang pembelian.')->withInput();
            }

            $storeId = $request->store_id ?: Store::where('company_id', $companyId)->first()?->id;

            $purchase = new Purchase();
            $purchase->company_id = $companyId;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->total_amount = $request->total_amount ?: 0;
            $purchase->status = $request->status ?: 'PENDING';
            $purchase->reference_number = $request->reference_number ?: null;
            $purchase->purchase_number = 'PUR-' . date('YmdHis');
            $purchase->save();

            $calculatedTotal = 0;

            foreach ($request->items as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity'])) continue;

                if (!Product::whereKey($item['product_id'])->when($companyId, fn($q)=>$q->where('company_id', $companyId))->exists()) {
                    continue;
                }

                $qty = (int) $item['quantity'];
                $cost = (float) ($item['cost'] ?? 0);
                $calculatedTotal += ($qty * $cost);

                $purchaseItem = new PurchaseItem();
                $purchaseItem->company_id = $companyId;
                $purchaseItem->purchase_id = $purchase->id;
                $purchaseItem->product_id = $item['product_id'];
                $purchaseItem->quantity = $qty;
                $purchaseItem->cost = $cost;
                $purchaseItem->save();

                if (strtoupper($purchase->status) === 'RECEIVED') {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->track_stock) {
                        $product->increment('stock_quantity', $qty);
                    }

                    $movementPayload = [
                        'company_id' => $companyId,
                        'store_id' => $storeId,
                        'product_id' => $item['product_id'],
                        'quantity' => $qty,
                        'type' => 'purchase',
                    ];
                    if (Schema::hasColumn('stock_movements', 'status')) {
                        $movementPayload['status'] = 'active';
                    }
                    StockMovement::create($movementPayload);
                }
            }

            if ($calculatedTotal > 0 && empty($request->total_amount)) {
                $purchase->total_amount = $calculatedTotal;
                $purchase->save();
            }

            DB::commit();
            return redirect()->to('admin-panel/purchase/listing')->with('success', "Data pembelian baru berhasil disimpan");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('failed', $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $purchase = Purchase::with(['purchase_items.product', 'supplier'])
            ->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))
            ->find($id);

        if (!$purchase) {
            return redirect()->to('admin-panel/purchase/listing')->with('failed', "Data pembelian tidak ditemukan atau tidak memiliki akses");
        }

        if (strtoupper($purchase->status) === 'RECEIVED') {
            return redirect()->to('admin-panel/purchase/listing')->with('failed', 'Pembelian berstatus RECEIVED tidak dapat diubah lagi.');
        }

        $companyId = $purchase->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        $stores = Store::where('company_id', $companyId)->orderBy('name')->get();
        return view('admin.pos.purchase.update', compact('purchase', 'suppliers', 'products', 'stores'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))
                ->find($id);

            if (!$purchase) {
                DB::rollBack();
                return redirect()->to('admin-panel/purchase/listing')->with('failed', 'Data pembelian tidak ditemukan atau tidak memiliki akses');
            }

            if (strtoupper($purchase->status) === 'RECEIVED') {
                DB::rollBack();
                return redirect()->to('admin-panel/purchase/listing')->with('failed', 'Pembelian berstatus RECEIVED tidak dapat diubah lagi.');
            }

            $inputs = $request->all();
            $errorMessage = $this->purchaseStoreVal->validate($inputs);
            if ($errorMessage) {
                DB::rollBack();
                return redirect()->back()->with('failed', $errorMessage);
            }

            $companyId = (auth()->user()?->isSuperAdmin() && $request->filled('company_id')) 
                ? $request->company_id 
                : $purchase->company_id;

            if (!Supplier::whereKey($request->supplier_id)->where('company_id', $companyId)->exists()) {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Supplier tidak valid untuk perusahaan ini.')->withInput();
            }

            if (!$request->has('items') || empty($request->items)) {
                DB::rollBack();
                return redirect()->back()->with('failed', 'Harap masukkan minimal 1 barang pembelian.')->withInput();
            }

            $storeId = $request->store_id ?: Store::where('company_id', $companyId)->first()?->id;

            $purchase->company_id = $companyId;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->status = $request->status;
            $purchase->reference_number = $request->reference_number ?: $purchase->reference_number;
            $purchase->total_amount = $request->total_amount ?: 0;
            $purchase->save();

            // Clear old items
            PurchaseItem::where('purchase_id', $purchase->id)->delete();

            $calculatedTotal = 0;

            foreach ($request->items as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity'])) continue;

                $qty = (int) $item['quantity'];
                $cost = (float) ($item['cost'] ?? 0);
                $calculatedTotal += ($qty * $cost);

                $purchaseItem = new PurchaseItem();
                $purchaseItem->company_id = $companyId;
                $purchaseItem->purchase_id = $purchase->id;
                $purchaseItem->product_id = $item['product_id'];
                $purchaseItem->quantity = $qty;
                $purchaseItem->cost = $cost;
                $purchaseItem->save();

                if (strtoupper($purchase->status) === 'RECEIVED') {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->track_stock) {
                        $product->increment('stock_quantity', $qty);
                    }

                    $movementPayload = [
                        'company_id' => $companyId,
                        'store_id' => $storeId,
                        'product_id' => $item['product_id'],
                        'quantity' => $qty,
                        'type' => 'purchase',
                    ];
                    if (Schema::hasColumn('stock_movements', 'status')) {
                        $movementPayload['status'] = 'active';
                    }
                    StockMovement::create($movementPayload);
                }
            }

            if ($calculatedTotal > 0 && empty($request->total_amount)) {
                $purchase->total_amount = $calculatedTotal;
                $purchase->save();
            }

            DB::commit();
            return redirect()->to('admin-panel/purchase/listing')->with('success', 'Data pembelian berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'purchase_items.product', 'company'])
            ->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))
            ->find($id);

        if (!$purchase) {
            return redirect()->to('admin-panel/purchase/listing')->with('failed', 'Data pembelian tidak ditemukan atau tidak memiliki akses');
        }

        return view('admin.pos.purchase.show', compact('purchase'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::with('purchase_items')
                ->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))
                ->find($id);

            if (!$purchase) {
                DB::rollBack();
                return redirect()->to('admin-panel/purchase/listing')->with('failed', "Data pembelian tidak ditemukan atau tidak memiliki akses");
            }

            $companyId = $purchase->company_id;
            $oldValue = $purchase->only(['id', 'status', 'total_amount']);
            $storeId = Store::where('company_id', $companyId)->first()?->id;

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
            return redirect()->to('admin-panel/purchase/listing')->with('success', 'Pembelian berhasil dibatalkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->to('admin-panel/purchase/listing')->with('failed', $e->getMessage());
        }
    }
}
