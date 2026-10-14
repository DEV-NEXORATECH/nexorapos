<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyPayment;
use App\Services\MidtransPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyPaymentController extends Controller
{
    public function __construct(private MidtransPaymentService $midtrans)
    {
    }

    public function createQris(Request $request)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $data = $request->validate(['company_id' => ['required', 'integer', 'exists:companies,id']]);
        $company = Company::findOrFail($data['company_id']);
        $amount = (int) round((float) $company->payment_amount);

        if ($amount < 1) {
            return response()->json(['message' => 'Isi Amount billing terlebih dahulu.'], 422);
        }

        $payment = CompanyPayment::create([
            'company_id' => $company->id,
            'order_id' => 'LICENSE-'.$company->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
            'amount' => $amount,
            'status' => 'pending',
        ]);

        try {
            $snap = $this->midtrans->createQrisTransaction($payment, $company->name);
            $payment->update(['snap_token' => $snap['token']]);

            return response()->json([
                'snap_token' => $snap['token'],
                'order_id' => $payment->order_id,
            ]);
        } catch (\Throwable $exception) {
            $payment->update(['status' => 'failed', 'raw_response' => ['message' => $exception->getMessage()]]);

            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }

    public function notification(Request $request)
    {
        $payload = $request->all();
        abort_unless($this->midtrans->verifyNotification($payload), 403);

        $payment = CompanyPayment::where('order_id', $payload['order_id'] ?? '')->firstOrFail();
        $status = $payload['transaction_status'] ?? 'pending';
        $isPaid = in_array($status, ['settlement', 'capture'], true)
            && (($status !== 'capture') || ($payload['fraud_status'] ?? 'accept') === 'accept');

        DB::transaction(function () use ($payment, $payload, $status, $isPaid) {
            $payment->update([
                'status' => $isPaid ? 'paid' : $status,
                'payment_type' => $payload['payment_type'] ?? null,
                'transaction_id' => $payload['transaction_id'] ?? null,
                'fraud_status' => $payload['fraud_status'] ?? null,
                'paid_at' => $isPaid ? ($payment->paid_at ?: now()) : $payment->paid_at,
                'raw_response' => $payload,
            ]);

            if ($isPaid && ! $payment->wasChanged('paid_at')) {
                return;
            }

            if ($isPaid) {
                $company = Company::lockForUpdate()->find($payment->company_id);
                if ($company) {
                    $company->subscription_status = 'active';
                    $company->is_active = true;
                    $company->blocked_at = null;
                    $company->block_reason = null;
                    $company->last_paid_at = now()->toDateString();
                    $company->license_started_at = $company->license_started_at ?: now()->toDateString();
                    $company->payment_due_at = $company->nextPaymentDueDate()->toDateString();
                    $company->save();
                }
            }
        });

        return response()->json(['ok' => true]);
    }
}
