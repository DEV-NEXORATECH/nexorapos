<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyPayment;
use App\Models\Pos\Payment;
use App\Models\Pos\Sale;
use App\Services\MidtransPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CompanyPaymentController extends Controller
{
    public function __construct(private MidtransPaymentService $midtrans)
    {
    }

    public function createQris(Request $request)
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if ($user->isSuperAdmin()) {
            $data = $request->validate(['company_id' => ['required', 'integer', 'exists:companies,id']]);
            $company = Company::findOrFail($data['company_id']);
        } else {
            $company = $user->company;
            abort_unless($company, 403);
        }
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

    public function createPosQris(Request $request)
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $data = $request->validate([
            'sale_id' => ['required', 'integer', 'exists:sales,id'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $company = $user->company;
        abort_unless($company, 403);

        $sale = Sale::where('id', $data['sale_id'])
            ->where('company_id', $company->id)
            ->firstOrFail();

        $amount = (int) round((float) $data['amount']);
        if ($amount < 1) {
            return response()->json(['message' => 'Nominal QRIS harus lebih dari 0.'], 422);
        }

        $payment = CompanyPayment::create([
            'company_id' => $company->id,
            'sale_id' => $sale->id,
            'purpose' => 'pos',
            'order_id' => 'POS-'.$sale->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
            'amount' => $amount,
            'status' => 'pending',
        ]);

        try {
            $snap = $this->midtrans->createQrisTransaction($payment, $company->name.' - '.$sale->invoice_number);
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
        abort_unless(abs((float) ($payload['gross_amount'] ?? 0) - (float) $payment->amount) < 0.001, 422, 'Nominal notifikasi tidak sesuai pembayaran.');
        $status = $payload['transaction_status'] ?? 'pending';
        $isPaid = in_array($status, ['settlement', 'capture'], true)
            && (($status !== 'capture') || ($payload['fraud_status'] ?? 'accept') === 'accept');

        DB::transaction(function () use ($payment, $payload, $status, $isPaid) {
            $payment = CompanyPayment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            if ($payment->status === 'paid' && ! $isPaid) return;

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

            if ($payment->purpose === 'pos') {
                $sale = Sale::whereKey($payment->sale_id)->lockForUpdate()->first();
                if ($sale && $isPaid && ! in_array(strtolower($sale->status), ['void', 'voided', 'cancelled'], true)) {
                    $paidNonQris = Payment::where('sale_id', $sale->id)
                        ->whereHas('method', fn ($query) => $query->whereRaw('LOWER(name) <> ?', ['qris']))
                        ->when(Schema::hasColumn('payments', 'status'), fn ($query) => $query->where('status', 'active'))
                        ->sum('amount');
                    $paidQris = CompanyPayment::where('sale_id', $sale->id)
                        ->where('purpose', 'pos')
                        ->where('status', 'paid')
                        ->sum('amount');
                    $paidTotal = (float) $paidNonQris + (float) $paidQris;

                    $sale->paid_amount = $paidTotal;
                    $sale->change_amount = max(0, $paidTotal - (float) $sale->total);
                    $sale->payment_status = $paidTotal >= (float) $sale->total ? 'PAID' : ($paidTotal > 0 ? 'PARTIAL' : 'UNPAID');
                    $sale->status = $paidTotal >= (float) $sale->total ? 'completed' : 'pending';
                    $sale->save();

                    if (Schema::hasColumn('payments', 'status')) {
                        Payment::where('sale_id', $sale->id)
                            ->whereHas('method', fn ($query) => $query->whereRaw('LOWER(name) = ?', ['qris']))
                            ->update(['status' => 'active']);
                    }
                }
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
