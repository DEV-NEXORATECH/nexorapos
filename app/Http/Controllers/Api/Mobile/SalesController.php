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
        $request->validate(['date' => ['nullable', 'date_format:Y-m-d'], 'store_id' => ['nullable', 'integer', 'min:1'], 'per_page' => ['nullable', 'integer', 'min:1', 'max:100'], 'page' => ['nullable', 'integer', 'min:1'], 'search' => ['nullable', 'string', 'max:255']]);
        $assignedStore = ! $request->user()->isSuperAdmin() ? (int) $request->user()->store_id : 0;
        $requestedStore = (int) $request->query('store_id');
        abort_if($assignedStore && $requestedStore && $assignedStore !== $requestedStore, 403, 'Toko di luar lingkup akun.');
        $storeId = $assignedStore ?: $requestedStore;
        if ($storeId) abort_unless(\App\Models\Pos\Store::where('company_id', $companyId)->whereKey($storeId)->exists(), 403);
        $start = $request->date ? \Carbon\CarbonImmutable::parse($request->date, 'Asia/Jakarta')->startOfDay()->utc() : null;

        $sales = Sale::with(['store:id,name,code', 'customer:id,name', 'user:id,name'])
            ->where('company_id', $companyId)
            ->when($storeId, fn ($q) => $q->where('store_id', $storeId))
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start)->where('created_at', '<', $start->addDay()))
            ->when($request->payment_status, fn ($query) => $query->where('payment_status', strtoupper($request->payment_status)))
            ->when($request->status, fn ($query) => $query->where('status', $request->status))
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
        ])->where('company_id', $this->companyId($request))->when($request->attributes->get('mobile_own_sale_only'), fn ($q) => $q->where('user_id', $request->user()->id))->when(! $request->user()->isSuperAdmin() && $request->user()->store_id, fn ($q) => $q->where('store_id', $request->user()->store_id))->findOrFail($id);

        $qris = CompanyPayment::where('company_id', $sale->company_id)->where('sale_id', $sale->id)->where('purpose', 'pos')->latest('id')->first();
        if ($qris && $qris->snap_token) $sale->setAttribute('qris_payment', ['order_id' => $qris->order_id, 'snap_token' => $qris->snap_token, 'amount' => (float) $qris->amount, 'status' => $qris->status]);
        return $this->json(true, 'Sale detail.', ['sale' => $sale]);
    }

    public function reconciliation(Request $request)
    {
        $companyId = $this->companyId($request);
        $request->validate(['date' => ['nullable', 'date_format:Y-m-d'], 'store_id' => ['nullable', 'integer', 'min:1']]);
        $date = $request->get('date', now('Asia/Jakarta')->toDateString());
        $assignedStore = ! $request->user()->isSuperAdmin() ? (int) $request->user()->store_id : 0;
        $requestedStore = (int) $request->query('store_id');
        abort_if($assignedStore && $requestedStore && $assignedStore !== $requestedStore, 403, 'Toko di luar lingkup akun.');
        $storeId = $assignedStore ?: $requestedStore;
        if ($storeId) abort_unless(\App\Models\Pos\Store::where('company_id', $companyId)->whereKey($storeId)->exists(), 403);
        $start = \Carbon\CarbonImmutable::parse($date, 'Asia/Jakarta')->startOfDay()->utc();
        $end = $start->addDay();

        $sales = Sale::where('company_id', $companyId)
            ->where('created_at', '>=', $start)->where('created_at', '<', $end)
            ->when($storeId, fn ($q) => $q->where('store_id', $storeId))
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

        $hourlySales = $sales->groupBy(fn ($sale) => $sale->created_at?->timezone('Asia/Jakarta')->format('H:00') ?? 'Unknown')
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

        abort_unless(! $request->user()->store_id || $request->user()->isSuperAdmin() || (int) $sale->store_id === (int) $request->user()->store_id, 403);
        abort_unless(app(\App\Services\MobileAccess::class)->allows($request->user(), 'pos.sales.view') || (int) $sale->user_id === (int) $request->user()->id, 403);
        $reservation = \Illuminate\Support\Facades\DB::transaction(function () use ($sale, $amount, $companyId) {
            $locked = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();
            abort_if(in_array(strtolower($locked->status), ['void', 'cancelled', 'voided'], true), 409, 'Invoice sudah dibatalkan.');
            $existing = CompanyPayment::where('company_id', $companyId)->where('sale_id', $locked->id)->where('purpose', 'pos')->first();
            if ($existing) {
                abort_if((int) $existing->amount !== $amount, 409, 'Nominal QRIS berbeda dari pembayaran sebelumnya.');
                return [$existing, false];
            }
            abort_if(strtolower($locked->payment_status) === 'paid', 409, 'Invoice sudah lunas.');
            $expected = Payment::where('sale_id', $locked->id)->whereHas('method', fn ($q) => $q->whereRaw('LOWER(TRIM(name)) = ?', ['qris']))->where('status', 'pending')->sum('amount');
            abort_unless($amount > 0 && (int) round($expected) === $amount, 422, 'Nominal QRIS harus sesuai invoice.');
            $payment = CompanyPayment::create(['company_id' => $companyId, 'sale_id' => $locked->id, 'purpose' => 'pos', 'order_id' => 'POS-MOBILE-'.$companyId.'-'.$locked->id, 'amount' => $amount, 'status' => 'pending']);
            return [$payment, true];
        });
        [$payment, $created] = $reservation;
        if (! $created && ! $payment->snap_token) return $this->json(false, 'Hasil pembuatan QRIS sebelumnya belum dapat dipastikan. Periksa status melalui admin; jangan membuat pembayaran baru.', ['order_id' => $payment->order_id], 409);
        if ($created) {
            try {
                $snap = $this->midtrans->createQrisTransaction($payment, $sale->invoice_number);
                $payment->update(['snap_token' => $snap['token']]);
            } catch (\Throwable $exception) {
                // A timeout may happen after Midtrans accepted the order. Preserve the reservation.
                $payment->update(['raw_response' => ['message' => 'Gateway result uncertain; reconcile existing order.']]);
                return $this->json(false, 'Status pembuatan QRIS belum dapat dipastikan. Periksa order yang sama melalui admin.', ['order_id' => $payment->order_id], 502);
            }
        }
        return $this->json(true, 'QRIS tersedia.', ['payment' => $payment->fresh(), 'snap_token' => $payment->snap_token, 'order_id' => $payment->order_id, 'amount' => (float) $payment->amount], $created ? 201 : 200);
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
