<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\CompanyPayment;
use App\Models\Pos\Payment;
use App\Models\Pos\Sale;
use App\Services\MobileAccess;
use App\Services\CancelMidtransQris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancelMobilePayment extends Controller
{
    public function __invoke(Request $request, int $id)
    {
        $request->validate(['reason' => ['required', 'string', 'max:1000']]);
        $user = $request->user();
        $companyId = $user->isSuperAdmin() && $request->query('company_id') ? (int) $request->query('company_id') : (int) $user->company_id;
        $sale = Sale::where('company_id', $companyId)->findOrFail($id);
        abort_unless($user->isSuperAdmin() || ! $user->store_id || (int) $sale->store_id === (int) $user->store_id, 403);
        abort_unless((int) $sale->user_id === (int) $user->id || app(MobileAccess::class)->allows($user, 'pos.sales.void'), 403, 'Hanya kasir pemilik transaksi atau petugas berizin yang dapat membatalkan.');

        // Same lock order as the gateway notification: CompanyPayment, then Sale.
        // Gateway cancel is idempotent through verified terminal status on retries.
        return DB::transaction(function () use ($request, $sale, $companyId) {
            $gateway = CompanyPayment::where('company_id', $companyId)->where('sale_id', $sale->id)->where('purpose', 'pos')->lockForUpdate()->first();
            $locked = Sale::where('company_id', $companyId)->whereKey($sale->id)->lockForUpdate()->firstOrFail();
            if (in_array(strtolower($locked->status), ['void', 'voided', 'cancelled'], true)) {
                // Legacy invoice voids did not necessarily revoke the gateway session.
                // Only our verified cancellation marker permits a gateway-free replay.
                if ($gateway && ! ($gateway->raw_response['mobile_cancellation_confirmed'] ?? false)) {
                    abort_if($gateway->status === 'paid', 409, 'Gateway sudah menerima pembayaran; hubungi admin untuk rekonsiliasi/refund.');
                    app(CancelMidtransQris::class)->cancel($gateway);
                    $gateway->update(['status' => 'cancel', 'raw_response' => array_merge($gateway->raw_response ?? [], ['mobile_cancellation_confirmed' => true])]);
                }
                return response()->json(['success' => true, 'message' => 'Transaksi sudah dibatalkan.', 'data' => ['sale' => $locked->load('payments.method')]]);
            }
            abort_if(strtolower($locked->payment_status) === 'paid' || ($gateway && $gateway->status === 'paid'), 409, 'Pembayaran sudah diterima. Gunakan prosedur refund.');
            abort_if((float) $locked->paid_amount > 0 || Payment::where('sale_id', $locked->id)->where('company_id', $companyId)->where('status', 'active')->where('amount', '>', 0)->exists(), 409, 'Ada pembayaran yang sudah diterima, termasuk split/cash. Hubungi admin untuk refund.');
            abort_unless(Payment::where('sale_id', $locked->id)->where('company_id', $companyId)->whereHas('method', fn ($q) => $q->whereRaw('LOWER(TRIM(name)) = ?', ['qris']))->exists(), 422, 'Fitur ini untuk invoice QRIS yang belum dibayar.');
            // Recheck after the sale lock in case QRIS creation won the race.
            $gateway = $gateway ?: CompanyPayment::where('company_id', $companyId)->where('sale_id', $locked->id)->where('purpose', 'pos')->lockForUpdate()->first();
            if ($gateway) {
                abort_if($gateway->status === 'paid', 409, 'QRIS sudah dibayar.');
                app(CancelMidtransQris::class)->cancel($gateway);
                $gateway->update(['status' => 'cancel', 'raw_response' => array_merge($gateway->raw_response ?? [], ['mobile_cancellation_confirmed' => true])]);
            }
            // The existing void implementation restores stock, voids payment rows,
            // records the actor/reason and audit log in this same transaction.
            return app(PosController::class)->voidSale($request, $locked->id);
        }, 3);
    }
}
