<?php

namespace App\Services;

use App\Models\CompanyPayment;
use Illuminate\Support\Facades\Http;

class CancelMidtransQris
{
    public function cancel(CompanyPayment $payment): void
    {
        $key = (string) config('services.midtrans.server_key');
        abort_if($key === '', 503, 'Konfigurasi pembayaran server belum lengkap.');
        abort_unless($payment->snap_token, 409, 'Pembuatan QRIS belum terkonfirmasi. Hubungi admin untuk memeriksa order yang sama.');
        $production = config('services.midtrans.is_production');
        $core = $production ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
        $snap = $production ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
        $order = rawurlencode($payment->order_id);
        try {
            $status = Http::withBasicAuth($key, '')->acceptJson()->timeout(20)->get($core.'/v2/'.$order.'/status');
            $code = (string) $status->json('status_code');
            abort_unless($status->successful() && $code === '200' || $code === '404', 502, 'Status Midtrans belum dapat dipastikan. Coba periksa kembali.');
            $state = $code === '404' ? 'not_charged' : $status->json('transaction_status');
            abort_if(in_array($state, ['settlement', 'capture'], true), 409, 'QRIS sudah dibayar. Pembatalan ditolak; periksa invoice atau refund melalui admin.');
            if ($state === 'pending') {
                $response = Http::withBasicAuth($key, '')->acceptJson()->timeout(20)->post($core.'/v2/'.$order.'/cancel');
                abort_unless($response->successful() && (string) $response->json('status_code') === '200' && $response->json('transaction_status') === 'cancel', 409, 'Midtrans belum mengonfirmasi pembatalan. Periksa status sebelum mencoba lagi.');
            } else {
                abort_unless(in_array($state, ['not_charged', 'cancel', 'expire', 'deny', 'failure'], true), 409, 'Status pembayaran belum aman untuk dibatalkan.');
            }
            // Revoke the Snap session too: a 404 Core status alone does not mean
            // an existing Snap token is unusable.
            $session = Http::withBasicAuth($key, '')->acceptJson()->timeout(20)->post($snap.'/snap/v1/transactions/'.rawurlencode($payment->snap_token).'/cancel');
            abort_unless($session->successful() && $session->json('canceled_at') || $session->json('error_messages.0') === 'token already canceled', 409, 'Sesi QRIS belum berhasil ditutup. Jangan mulai transaksi baru; periksa kembali.');
        } catch (\Illuminate\Http\Client\ConnectionException $exception) {
            abort(502, 'Koneksi pembayaran terputus. Status belum pasti; periksa transaksi yang sama, jangan buat pembayaran baru.');
        }
    }
}
