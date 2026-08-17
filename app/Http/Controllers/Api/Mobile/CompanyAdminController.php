<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompanyAdminController extends Controller
{
    public function store(Request $request)
    {
        $this->superAdminOnly($request);

        $data = $request->validate($this->companyRules() + [
            'name' => ['required', 'string', 'max:255'],
        ]);

        $company = Company::create($data + [
            'company_code' => $this->generateCompanyCode($data['name']),
            'is_active' => $data['is_active'] ?? true,
            'subscription_status' => $data['subscription_status'] ?? 'active',
            'billing_period' => $data['billing_period'] ?? 'monthly',
            'billing_interval_months' => $data['billing_interval_months'] ?? 1,
            'license_started_at' => $data['license_started_at'] ?? now()->toDateString(),
            'payment_due_at' => $data['payment_due_at'] ?? now()->addMonth()->toDateString(),
            'grace_period_days' => $data['grace_period_days'] ?? 7,
            'max_users' => $data['max_users'] ?? 10,
        ]);

        Feature::query()->pluck('key')->each(function ($featureKey) use ($company) {
            CompanyFeature::updateOrCreate(
                ['company_id' => $company->id, 'feature_key' => $featureKey],
                ['is_enabled' => true]
            );
        });

        return $this->json(true, 'Company berhasil dibuat.', ['company' => $company->fresh(), 'license' => $company->licenseSnapshot()], 201);
    }

    public function update(Request $request, int $id)
    {
        $this->superAdminOnly($request);
        $company = Company::findOrFail($id);
        $data = $request->validate($this->companyRules());
        unset($data['company_code']);

        $company->fill($data)->save();

        return $this->json(true, 'Company berhasil diupdate.', ['company' => $company->fresh(), 'license' => $company->licenseSnapshot()]);
    }

    public function updateProfile(Request $request)
    {
        $company = $this->companyForRequest($request);
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'npwp' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'string', 'max:255'],
        ]);

        unset($data['company_code']);
        $company->fill($data)->save();

        return $this->json(true, 'Profile company berhasil diupdate.', ['company' => $company->fresh()]);
    }

    public function updateLicense(Request $request, int $id)
    {
        $this->superAdminOnly($request);
        $company = Company::findOrFail($id);
        $data = $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'subscription_status' => ['nullable', Rule::in(['active', 'inactive', 'blocked'])],
            'billing_period' => ['nullable', Rule::in(['monthly', 'yearly', 'custom'])],
            'billing_interval_months' => ['nullable', 'integer', 'min:1'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'last_paid_at' => ['nullable', 'date'],
            'license_started_at' => ['nullable', 'date'],
            'payment_due_at' => ['nullable', 'date'],
            'grace_period_days' => ['nullable', 'integer', 'min:0'],
            'max_users' => ['nullable', 'integer', 'min:1'],
            'blocked_at' => ['nullable', 'date'],
            'block_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $company->fill($data)->save();

        return $this->json(true, 'License company berhasil diupdate.', ['company' => $company->fresh(), 'license' => $company->licenseSnapshot()]);
    }

    public function updateFeatures(Request $request, int $id)
    {
        $this->superAdminOnly($request);
        $company = Company::findOrFail($id);
        $data = $request->validate([
            'features' => ['required', 'array'],
            'features.*' => ['boolean'],
        ]);

        foreach ($data['features'] as $featureKey => $enabled) {
            if (! Feature::where('key', $featureKey)->exists()) {
                continue;
            }

            CompanyFeature::updateOrCreate(
                ['company_id' => $company->id, 'feature_key' => $featureKey],
                ['is_enabled' => (bool) $enabled]
            );
        }

        return $this->json(true, 'Feature company berhasil diupdate.', [
            'company' => $company->fresh(),
            'features' => CompanyFeature::where('company_id', $company->id)->get(),
        ]);
    }

    private function companyRules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'npwp' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'subscription_status' => ['nullable', Rule::in(['active', 'inactive', 'blocked'])],
            'billing_period' => ['nullable', Rule::in(['monthly', 'yearly', 'custom'])],
            'billing_interval_months' => ['nullable', 'integer', 'min:1'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'last_paid_at' => ['nullable', 'date'],
            'license_started_at' => ['nullable', 'date'],
            'payment_due_at' => ['nullable', 'date'],
            'grace_period_days' => ['nullable', 'integer', 'min:0'],
            'max_users' => ['nullable', 'integer', 'min:1'],
        ];
    }

    private function generateCompanyCode(string $name): string
    {
        $prefix = strtoupper(Str::of($name)->slug('')->substr(0, 6) ?: 'COMP');
        $number = str_pad((string) (Company::count() + 1), 3, '0', STR_PAD_LEFT);
        $code = $prefix.'-'.$number;
        $attempt = 1;

        while (Company::where('company_code', $code)->exists()) {
            $attempt++;
            $code = $prefix.'-'.str_pad((string) (Company::count() + $attempt), 3, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    private function companyForRequest(Request $request): Company
    {
        if ($request->user()?->isSuperAdmin()) {
            return Company::findOrFail($request->input('company_id') ?: $request->query('company_id'));
        }

        return Company::findOrFail($request->user()->company_id);
    }

    private function superAdminOnly(Request $request): void
    {
        abort_if(! $request->user()?->isSuperAdmin(), 403, 'Hanya Super Admin.');
    }

    private function json(bool $success, string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(compact('success', 'message', 'data'), $status);
    }
}
