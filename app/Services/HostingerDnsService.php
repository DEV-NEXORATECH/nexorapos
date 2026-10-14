<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class HostingerDnsService
{
    public function createFor(Company $company): void
    {
        $subdomain = $company->subdomain ?: $this->uniqueSubdomain($company->name, $company->id);
        $company->forceFill([
            'subdomain' => $subdomain,
            'domain_status' => 'pending',
            'domain_error' => null,
        ])->save();

        $token = (string) config('services.hostinger.api_token');
        $domain = (string) config('services.hostinger.domain');
        $target = (string) config('services.hostinger.record_target');
        $type = strtoupper((string) config('services.hostinger.record_type', 'CNAME'));

        if ($token === '' || $domain === '' || $target === '') {
            $company->forceFill([
                'domain_status' => 'pending',
                'domain_error' => 'Hostinger DNS belum dikonfigurasi. Isi HOSTINGER_API_TOKEN dan HOSTINGER_DNS_RECORD_TARGET.',
            ])->save();

            return;
        }

        if (! in_array($type, ['A', 'CNAME'], true)) {
            throw new RuntimeException('HOSTINGER_DNS_RECORD_TYPE harus A atau CNAME.');
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(20)
            ->put('https://developers.hostinger.com/api/dns/v1/zones/'.rawurlencode($domain), [
                'overwrite' => false,
                'zone' => [[
                    'name' => $subdomain,
                    'records' => [['content' => rtrim($target, '.')]],
                    'ttl' => (int) config('services.hostinger.ttl', 300),
                    'type' => $type,
                ]],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Hostinger DNS gagal (HTTP '.$response->status().'): '.Str::limit($response->body(), 500));
        }

        $company->forceFill([
            'domain_status' => 'active',
            'domain_error' => null,
            'domain_created_at' => now(),
        ])->save();
    }

    private function uniqueSubdomain(string $name, ?int $companyId = null): string
    {
        $base = Str::slug($name) ?: 'company';
        $base = Str::limit($base, 50, '');
        $candidate = $base;
        $counter = 2;

        while (Company::where('subdomain', $candidate)
            ->when($companyId, fn ($query) => $query->where('id', '!=', $companyId))
            ->exists()) {
            $candidate = $base.'-'.$counter++;
        }

        return $candidate;
    }
}
