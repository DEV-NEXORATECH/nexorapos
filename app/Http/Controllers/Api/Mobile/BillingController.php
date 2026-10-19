<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyPayment;
use App\Services\MidtransPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    public function __construct(private MidtransPaymentService $midtrans)
    {
    }

    public function createQris(Request $request)
    {
        $company = $this->company($request);
        $amount = (int) round((float) ($request->input('amount') ?: $company->payment_amount));

        if ($amount < 1) {
            return $this->json(false, 'Nominal license belum diatur.', null, 422);
        }

        $payment = CompanyPayment::create([
            'company_id' => $company->id,
            'order_id' => 'LICENSE-'.$company->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
            'amount' => $amount,
            'status' => 'pending',
            'purpose' => 'license',
        ]);

        try {
            $snap = $this->midtrans->createQrisTransaction($payment, $company->name);
            $payment->update(['snap_token' => $snap['token']]);
        } catch (\Throwable $exception) {
            $payment->update(['status' => 'failed', 'raw_response' => ['message' => $exception->getMessage()]]);

            return $this->json(false, $exception->getMessage(), null, 422);
        }

        return $this->json(true, 'QRIS license berhasil dibuat.', [
            'payment' => $payment->fresh(),
            'snap_token' => $snap['token'],
            'order_id' => $payment->order_id,
        ], 201);
    }

    public function status(Request $request, string $orderId)
    {
        $payment = CompanyPayment::where('company_id', $this->company($request)->id)
            ->where('purpose', 'license')
            ->where('order_id', $orderId)
            ->firstOrFail();

        return $this->json(true, 'License payment status.', [
            'order_id' => $payment->order_id,
            'status' => $payment->status,
            'amount' => (float) $payment->amount,
            'paid_at' => $payment->paid_at,
        ]);
    }

    private function company(Request $request): Company
    {
        $id = $request->user()?->isSuperAdmin()
            ? ($request->input('company_id') ?: $request->query('company_id'))
            : $request->user()?->company_id;

        abort_if(! $id, 422, 'company_id wajib untuk Super Admin.');

        return Company::findOrFail($id);
    }

    private function json(bool $success, string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(compact('success', 'message', 'data'), $status);
    }
}
