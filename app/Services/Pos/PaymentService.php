<?php
namespace App\Services\Pos;

use Illuminate\Http\Request;
use App\Models\Pos\Payment;
use App\Models\Pos\Sale;
use App\Models\Pos\Method;
use App\Pos\Payment\PaymentStoreValidation;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Schema;

class PaymentService
{
    public $payment;

    public function __construct()
    {
        $this->payment = new PaymentStoreValidation();
    }

    public function index()
    {
        if (request()->ajax()) {
            $user = auth()->user();
            $data = Payment::with(['company', 'sale', 'method'])
                ->when(!$user?->isSuperAdmin(), function ($q) use ($user) {
                    $q->where('company_id', $user?->company_id);
                })
                ->latest('id');

            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('id', function ($row) {
                    return $row->id;
                })
                ->addColumn('sale_ref', function ($row) {
                    if (!$row->sale) {
                        return '-';
                    }
                    $inv = $row->sale->invoice_number ?: ('#' . $row->sale->id);
                    return '<span class="font-weight-bold text-primary">' . e($inv) . '</span>';
                })
                ->addColumn('sale_status', function ($row) {
                    if (!$row->sale) {
                        return '-';
                    }
                    $status = strtoupper($row->sale->status ?? 'COMPLETED');
                    $badgeClass = match ($status) {
                        'COMPLETED' => 'is-success',
                        'PENDING' => 'is-warning',
                        'CANCELLED', 'VOID' => 'is-danger',
                        default => 'is-info'
                    };
                    return '<span class="nexora-badge ' . $badgeClass . '">' . e($status) . '</span>';
                })
                ->addColumn('method', function ($row) {
                    return $row->method ? e($row->method->name) : (e($row->payment_method) ?: '-');
                })
                ->addColumn('amount', function ($row) {
                    return '<span class="font-weight-bold text-success">Rp ' . number_format((float) $row->amount, 0, ',', '.') . '</span>';
                })
                ->addColumn('reference', function ($row) {
                    return e($row->reference ?: '-');
                })
                ->addColumn('status', function ($row) {
                    $status = strtoupper($row->status ?? 'ACTIVE');
                    $badgeClass = ($status === 'VOID' || $status === 'CANCELLED') ? 'is-danger' : 'is-success';
                    return '<span class="nexora-badge ' . $badgeClass . '">' . e($status) . '</span>';
                })
                ->addColumn('company', function ($row) {
                    return $row->company ? e($row->company->name) : '-';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = url('admin-panel/payments/edit/' . $row->id);
                    $delUrl = url('admin-panel/payments/delete/' . $row->id);
                    return '<div class="nexora-action-group">'
                        . '<a href="' . $editUrl . '" class="nexora-action-btn is-warning" title="Edit Pembayaran"><i class="fas fa-edit"></i></a>'
                        . '<a href="' . $delUrl . '" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Void/Batalkan catatan pembayaran ini?\')" title="Void Pembayaran"><i class="fas fa-trash"></i></a>'
                        . '</div>';
                })
                ->rawColumns(['sale_ref', 'sale_status', 'amount', 'status', 'action'])
                ->make(true);
        }

        return view('admin.pos.payment.listing');
    }

    public function add()
    {
        $user = auth()->user();
        $companyId = $user?->isSuperAdmin() ? request('company_id', $user?->company_id) : $user?->company_id;
        $sales = Sale::when($companyId, fn ($q) => $q->where('company_id', $companyId))->latest('id')->take(100)->get();
        $methods = Method::when($companyId, fn ($q) => $q->where('company_id', $companyId))->get();

        return view('admin.pos.payment.add', compact('sales', 'methods', 'companyId'));
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $companyId = $user?->isSuperAdmin() ? ($request->company_id ?? $user?->company_id) : $user?->company_id;

            $inputs = $request->all();
            $errorMessage = $this->payment->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage)->withInput();
            }

            if (! Sale::whereKey($request->sale_id)->when($companyId, fn ($q) => $q->where('company_id', $companyId))->exists()) {
                return redirect()->back()->with('failed', 'Penjualan/Sale tidak valid atau tidak sesuai dengan perusahaan.')->withInput();
            }

            if (! Method::whereKey($request->method_id)->when($companyId, fn ($q) => $q->where('company_id', $companyId))->exists()) {
                return redirect()->back()->with('failed', 'Metode pembayaran tidak valid atau tidak sesuai dengan perusahaan.')->withInput();
            }

            $payment = new Payment();
            $payment->company_id = $companyId;
            $payment->sale_id = $request->sale_id;
            $payment->method_id = $request->method_id;
            $payment->amount = $request->amount;
            $payment->reference = $request->reference;

            if (Schema::hasColumn('payments', 'status')) {
                $payment->status = 'active';
            }

            $payment->save();

            AuditLogger::log('create', 'payments', $payment, null, $payment->only(['id', 'sale_id', 'method_id', 'amount', 'reference', 'status']));

            return redirect()->to('admin-panel/payments/listing')->with('success', 'Catatan pembayaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Gagal menyimpan pembayaran: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $payment = Payment::when(!$user?->isSuperAdmin(), fn ($q) => $q->where('company_id', $user?->company_id))->find($id);

        if (!$payment) {
            return redirect()->to('admin-panel/payments/listing')->with('failed', 'Catatan pembayaran tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $companyId = $payment->company_id;
        $sales = Sale::when($companyId, fn ($q) => $q->where('company_id', $companyId))->latest('id')->take(100)->get();
        $methods = Method::when($companyId, fn ($q) => $q->where('company_id', $companyId))->get();

        return view('admin.pos.payment.update', compact('payment', 'sales', 'methods', 'companyId'));
    }

    public function update(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $payment = Payment::when(!$user?->isSuperAdmin(), fn ($q) => $q->where('company_id', $user?->company_id))->find($id);

            if (!$payment) {
                return redirect()->to('admin-panel/payments/listing')->with('failed', 'Catatan pembayaran tidak ditemukan atau Anda tidak memiliki akses.');
            }

            $inputs = $request->all();
            $errorMessage = $this->payment->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage)->withInput();
            }

            $companyId = $user?->isSuperAdmin() ? ($request->company_id ?? $payment->company_id) : $payment->company_id;

            if (! Sale::whereKey($request->sale_id)->when($companyId, fn ($q) => $q->where('company_id', $companyId))->exists()) {
                return redirect()->back()->with('failed', 'Penjualan/Sale tidak valid untuk perusahaan ini.')->withInput();
            }

            if (! Method::whereKey($request->method_id)->when($companyId, fn ($q) => $q->where('company_id', $companyId))->exists()) {
                return redirect()->back()->with('failed', 'Metode pembayaran tidak valid untuk perusahaan ini.')->withInput();
            }

            $oldValue = $payment->only(['id', 'sale_id', 'method_id', 'amount', 'reference', 'status']);

            if ($user?->isSuperAdmin() && $request->filled('company_id')) {
                $payment->company_id = $request->company_id;
            }

            $payment->sale_id = $request->sale_id;
            $payment->method_id = $request->method_id;
            $payment->amount = $request->amount;
            $payment->reference = $request->reference;
            $payment->save();

            AuditLogger::log('update', 'payments', $payment, $oldValue, $payment->only(['id', 'sale_id', 'method_id', 'amount', 'reference', 'status']));

            return redirect()->to('admin-panel/payments/listing')->with('success', 'Catatan pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Gagal memperbarui pembayaran: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $payment = Payment::when(!$user?->isSuperAdmin(), fn ($q) => $q->where('company_id', $user?->company_id))->find($id);

        if (!$payment) {
            return redirect()->back()->with('failed', 'Catatan pembayaran tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $oldValue = $payment->only(['id', 'sale_id', 'method_id', 'amount', 'status']);
        if (Schema::hasColumn('payments', 'status')) {
            $payment->status = 'void';
        }
        if (Schema::hasColumn('payments', 'voided_at')) {
            $payment->voided_at = now();
        }
        $payment->save();

        AuditLogger::log('void', 'payments', $payment, $oldValue, $payment->only(['id', 'sale_id', 'method_id', 'amount', 'status']));

        return redirect()->to('admin-panel/payments/listing')->with('success', 'Catatan pembayaran berhasil dibatalkan (void).');
    }
}
