<?php

namespace App\Services;

use App\Models\CompanyPayment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransPaymentService
{
    public function createQrisTransaction(CompanyPayment $payment, string $companyName): array
    {
        $serverKey = (string) config('services.midtrans.server_key');
        if ($serverKey === '') {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi.');
        }

        $baseUrl = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->timeout(30)
            ->post($baseUrl.'/snap/v1/transactions', [
                'transaction_details' => [
                    'order_id' => $payment->order_id,
                    'gross_amount' => (int) $payment->amount,
                ],
                'item_details' => [[
                    'id' => 'LICENSE-'.$payment->company_id,
                    'price' => (int) $payment->amount,
                    'quantity' => 1,
                    'name' => 'Subscription '.$companyName,
                ]],
                'enabled_payments' => ['qris'],
                'credit_card' => [
                    'secure' => (bool) config('services.midtrans.is_3ds', true),
                ],
            ]);

        if ($response->failed() || ! $response->json('token')) {
            throw new RuntimeException('Midtrans gagal membuat pembayaran: '.($response->json('error_messages.0') ?: $response->body()));
        }

        return $response->json();
    }

    public function verifyNotification(array $payload): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');
        $signature = hash('sha512',
            ($payload['order_id'] ?? '').
            ($payload['status_code'] ?? '').
            ($payload['gross_amount'] ?? '').
            $serverKey
        );

        return hash_equals($signature, (string) ($payload['signature_key'] ?? ''));
    }
}
