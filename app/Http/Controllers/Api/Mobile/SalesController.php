<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\CompanyPayment;
use App\Models\Pos\Payment;
use App\Models\Pos\Sale;
use App\Services\MidtransPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    public function __construct(private MidtransPaymentService $midtrans)
    {
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId($request);

        $sales = Sale::with(['store:id,name,code', 'customer:id,name', 'user:id,name'])
            ->where('company_id', $companyId)
            ->when($request->date, fn ($query) => $query->whereDate('created_at', $request->date))
            ->when($request->status, fn ($query) => $query->where('status', $request->status))
            ->when($request->store_id, fn ($query) => $query->where('store_id', $request->store_id))
            ->when($request->search, function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('invoice_number', 'like', '%'.$search.'%')
                        ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->latest('created_at');

        return $this->json(true, 'Sales list.', [
            'sales' => $sales->paginate(min((int) $request->get('per_page', 20), 100)),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $sale = Sale::with([
            'store:id,name,code',
            'customer:id,name,email,phone',
            'user:id,name',
            'sale_items.product:id,name,sku,barcode',
            'sale_items.product_variant',
            'payments.method:id,name',
        ])->where('company_id', $this->companyId($request))->findOrFail($id);

        return $this->json(true, 'Sale detail.', ['sale' => $sale]);
    }

    public function reconciliation(Request $request)
    {
        $companyId = $this->companyId($request);
        $date = $request->get('date', now()->toDateString());

        $sales = Sale::where('company_id', $companyId)
            ->whereDate('created_at', $date)
            ->whereNotIn('status', ['cancelled', 'void'])
            ->get();

        $payments = Payment::with('method:id,name')
            ->where('company_id', $companyId)
            ->whereIn('sale_id', $sales->pluck('id'))
            ->when(Schema::hasColumn('payments', 'status'), fn ($query) => $query->where('status', 'active'))
            ->get();

        $paymentBreakdown = $payments->groupBy(fn ($payment) => $payment->method?->name ?? 'Unknown')
            ->map(fn ($rows, $method) => [
                'method' => $method,
                'transactions' => $rows->count(),
                'amount' => (float) $rows->sum('amount'),
            ])->values()->all();

        $hourlySales = $sales->groupBy(fn ($sale) => $sale->created_at?->format('H:00') ?? 'Unknown')
            ->map(fn ($rows, $hour) => [
                'hour' => $hour,
                'transactions' => $rows->count(),
                'amount' => (float) $rows->sum('total'),
            ])->sortBy('hour')->values()->all();

        $totalSales = (float) $sales->sum('total');
        $totalPaid = (float) $payments->sum('amount');

        return $this->json(true, 'Reconciliation report.', [
            'date' => $date,
            'summary' => [
                'receipt_count' => $sales->count(),
                'net_sales' => $totalSales,
                'payment_total' => $totalPaid,
                'difference' => round($totalPaid - $totalSales, 2),
            ],
            'payments_by_method' => $paymentBreakdown,
            'hourly_sales' => $hourlySales,
        ]);
    }

    public function createQris(Request $request)
    {
        $data = $request->validate([
            'sale_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $companyId = $this->companyId($request);
        $sale = Sale::where('company_id', $companyId)->findOrFail($data['sale_id']);
        $amount = (int) round((float) $data['amount']);

        $payment = CompanyPayment::create([
            'company_id' => $companyId,
            'sale_id' => $sale->id,
            'purpose' => 'pos',
            'order_id' => 'POS-'.$sale->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
            'amount' => $amount,
            'status' => 'pending',
        ]);

        try {
            $snap = $this->midtrans->createQrisTransaction($payment, $sale->invoice_number);
            $payment->update(['snap_token' => $snap['token']]);
        } catch (\Throwable $exception) {
            $payment->update(['status' => 'failed', 'raw_response' => ['message' => $exception->getMessage()]]);

            return $this->json(false, $exception->getMessage(), null, 422);
        }

        return $this->json(true, 'QRIS berhasil dibuat.', [
            'payment' => $payment->fresh(),
            'snap_token' => $snap['token'],
            'order_id' => $payment->order_id,
        ], 201);
    }

    public function qrisStatus(Request $request, string $orderId)
    {
        $payment = CompanyPayment::where('company_id', $this->companyId($request))
            ->where('purpose', 'pos')
            ->where('order_id', $orderId)
            ->firstOrFail();

        return $this->json(true, 'QRIS payment status.', [
            'order_id' => $payment->order_id,
            'status' => $payment->status,
            'amount' => (float) $payment->amount,
            'paid_at' => $payment->paid_at,
            'sale_id' => $payment->sale_id,
        ]);
    }

    private function companyId(Request $request): int
    {
        if ($request->user()?->isSuperAdmin() && $request->query('company_id')) {
            return (int) $request->query('company_id');
        }

        return (int) $request->user()->company_id;
    }

    private function json(bool $success, string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(compact('success', 'message', 'data'), $status);
    }
}
