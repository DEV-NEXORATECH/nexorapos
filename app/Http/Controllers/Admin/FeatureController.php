<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\Role;
use App\Models\User;
use App\Services\CompanyCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Support\AuditLogger;

class FeatureController extends Controller
{
    public function __construct(private CompanyCodeGenerator $companyCodeGenerator)
    {
    }

    private const BILLING_COLUMNS = [
        'subscription_status',
        'billing_period',
        'billing_interval_months',
        'payment_amount',
        'last_paid_at',
        'license_started_at',
        'payment_due_at',
        'grace_period_days',
        'blocked_at',
        'block_reason',
        'max_users',
    ];

    public function index()
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage company features.');
        }

        $setupWarnings = $this->setupWarnings();
        $companies = Company::orderBy('name')->get();
        $features = Schema::hasTable('features')
            ? Feature::orderBy('display_order')->orderBy('name')->get()
            : collect();
        $companyFeatures = Schema::hasTable('company_features')
            ? CompanyFeature::all()->groupBy('company_id')->map(fn ($items) => $items->keyBy('feature_key'))
            : collect();

        return view('admin.features.index', compact('companies', 'features', 'companyFeatures', 'setupWarnings'));
    }

    public function create()
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage company features.');
        }

        return view('admin.features.create');
    }

    public function show($id)
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage company features.');
        }

        $company = Company::findOrFail($id);
        $features = Schema::hasTable('features')
            ? Feature::orderBy('display_order')->orderBy('name')->get()
            : collect();
        $companyFeatures = Schema::hasTable('company_features')
            ? CompanyFeature::where('company_id', $id)->get()->keyBy('feature_key')
            : collect();

        return view('admin.features.show', compact('company', 'features', 'companyFeatures'));
    }

    public function storeCompany(Request $request)
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Only Super Admin can create companies.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'npwp' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'subscription_status' => ['required', 'in:active,past_due,blocked,inactive'],
            'billing_period' => ['required', 'in:monthly,yearly,custom'],
            'billing_interval_months' => ['required', 'integer', 'min:1', 'max:36'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'license_started_at' => ['nullable', 'date'],
            'payment_due_at' => ['nullable', 'date'],
            'grace_period_days' => ['required', 'integer', 'min:0', 'max:90'],
            'max_users' => ['required', 'integer', 'min:1', 'max:100000'],
            'admin_name' => ['nullable', 'required_with:admin_email', 'string', 'max:255'],
            'admin_email' => ['nullable', 'required_with:admin_name', 'email', 'unique:users,email'],
            'admin_phone' => ['nullable', 'required_with:admin_email', 'string', 'max:30'],
            'admin_password' => ['nullable', 'required_with:admin_email', 'string', 'min:8'],
        ]);

        // MySQL strict mode rejects an empty string for nullable decimal columns.
        $data['payment_amount'] = ($data['payment_amount'] ?? '') === ''
            ? null
            : $data['payment_amount'];

        $company = DB::transaction(function () use ($data) {
            $company = new Company();
            $companyPayload = [
                'name' => $data['name'],
                'company_code' => $this->companyCodeGenerator->generate($data['name']),
                'is_active' => ! in_array($data['subscription_status'], ['blocked', 'inactive'], true),
                'subscription_status' => $data['subscription_status'],
                'billing_period' => $data['billing_period'],
                'billing_interval_months' => match ($data['billing_period']) {
                    'monthly' => 1,
                    'yearly' => 12,
                    default => (int) $data['billing_interval_months'],
                },
                'payment_amount' => $data['payment_amount'] ?? null,
                'license_started_at' => $data['license_started_at'] ?? now()->toDateString(),
                'payment_due_at' => $data['payment_due_at'] ?? now()->addMonth()->toDateString(),
                'grace_period_days' => $data['grace_period_days'],
                'max_users' => $data['max_users'],
            ];

            foreach (['legal_name', 'npwp', 'email', 'phone', 'address'] as $column) {
                if (Schema::hasColumn('companies', $column)) {
                    $companyPayload[$column] = $data[$column] ?? null;
                }
            }

            $company->forceFill($companyPayload)->save();

            if (Schema::hasTable('features') && Schema::hasTable('company_features')) {
                Feature::query()->pluck('key')->each(function ($featureKey) use ($company) {
                    CompanyFeature::updateOrCreate(
                        ['company_id' => $company->id, 'feature_key' => $featureKey],
                        ['is_enabled' => true]
                    );
                });
            }

            if (! empty($data['admin_email'])) {
                $role = Role::withoutGlobalScopes()->firstOrCreate([
                    'role' => 'Admin',
                    'company_id' => $company->id,
                ]);

                User::create([
                    'name' => $data['admin_name'],
                    'email' => $data['admin_email'],
                    'phone' => $data['admin_phone'],
                    'password' => Hash::make($data['admin_password']),
                    'role_id' => $role->id,
                    'company_id' => $company->id,
                    'is_super_admin' => false,
                    'email_verified_at' => now(),
                ]);
            }

            AuditLogger::log('create', 'companies', $company, [], $company->only(['id', 'name', 'company_code', 'subscription_status', 'max_users']));

            return $company;
        });

        return redirect('admin-panel/features')->with(
            'success',
            'Company berhasil dibuat. Company Code: '.$company->company_code.'. Berikan kode ini ke admin company untuk login.'
        )->with('created_company_code', $company->company_code);
    }

    public function updateBilling(Request $request)
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage company billing.');
        }

        $missingColumns = $this->missingCompanyColumns();
        if ($missingColumns !== []) {
            return redirect()->back()->with(
                'failed',
                'Setup billing belum lengkap. Kolom companies belum ada: '.implode(', ', $missingColumns).'. Jalankan migration terbaru dulu.'
            );
        }

        $data = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'subscription_status' => ['required', 'in:active,past_due,blocked,inactive'],
            'billing_period' => ['required', 'in:monthly,yearly,custom'],
            'billing_interval_months' => ['required', 'integer', 'min:1', 'max:36'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'max_users' => ['required', 'integer', 'min:1', 'max:100000'],
            'license_started_at' => ['nullable', 'date'],
            'payment_due_at' => ['nullable', 'date'],
            'grace_period_days' => ['required', 'integer', 'min:0', 'max:90'],
            'block_reason' => ['nullable', 'string', 'max:255'],
            'billing_action' => ['nullable', 'in:save,mark_paid'],
        ]);

        $company = Company::findOrFail($data['company_id']);
        $oldValue = $company->only([
            'subscription_status',
            'billing_period',
            'billing_interval_months',
            'payment_amount',
            'max_users',
            'license_started_at',
            'payment_due_at',
            'grace_period_days',
            'blocked_at',
            'block_reason',
            'is_active',
        ]);
        $intervalMonths = match ($data['billing_period']) {
            'yearly' => 12,
            'monthly' => 1,
            default => (int) $data['billing_interval_months'],
        };

        $company->subscription_status = $data['subscription_status'];
        $company->billing_period = $data['billing_period'];
        $company->billing_interval_months = $intervalMonths;
        $company->payment_amount = $data['payment_amount'] ?? null;
        $company->max_users = $data['max_users'];
        $company->license_started_at = $data['license_started_at'] ?? $company->license_started_at;
        $company->payment_due_at = $data['payment_due_at'] ?? $company->payment_due_at;
        $company->grace_period_days = $data['grace_period_days'];
        $company->block_reason = $data['block_reason'] ?? null;
        $company->blocked_at = $data['subscription_status'] === 'blocked' ? now() : null;
        $company->is_active = ! in_array($data['subscription_status'], ['blocked', 'inactive'], true);

        if (($data['billing_action'] ?? 'save') === 'mark_paid') {
            $company->subscription_status = 'active';
            $company->is_active = true;
            $company->blocked_at = null;
            $company->block_reason = null;
            $company->last_paid_at = now()->toDateString();
            $company->license_started_at = $company->license_started_at ?: now()->toDateString();
            $company->payment_due_at = $company->nextPaymentDueDate()->toDateString();
        }

        $company->save();
        AuditLogger::log('update_billing', 'company_features', $company, $oldValue, $company->only(array_keys($oldValue)));

        return redirect()->back()->with('success', 'Company billing and license period updated successfully.');
    }

    public function update(Request $request)
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage company features.');
        }

        if (! Schema::hasTable('features') || ! Schema::hasTable('company_features')) {
            return redirect()->back()->with(
                'failed',
                'Setup fitur belum lengkap. Tabel features/company_features belum ada. Jalankan migration SaaS license dulu.'
            );
        }

        $data = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'features' => ['array'],
            'features.*' => ['string', 'exists:features,key'],
        ]);

        $enabled = collect($data['features'] ?? []);
        $oldValue = CompanyFeature::where('company_id', $data['company_id'])
            ->pluck('is_enabled', 'feature_key')
            ->all();

        Feature::query()->pluck('key')->each(function ($featureKey) use ($data, $enabled) {
            CompanyFeature::updateOrCreate(
                [
                    'company_id' => $data['company_id'],
                    'feature_key' => $featureKey,
                ],
                [
                    'is_enabled' => $enabled->contains($featureKey),
                ]
            );
        });
        $company = Company::findOrFail($data['company_id']);
        $newValue = CompanyFeature::where('company_id', $data['company_id'])
            ->pluck('is_enabled', 'feature_key')
            ->all();
        AuditLogger::log('update_features', 'company_features', $company, $oldValue, $newValue);

        return redirect()->back()->with('success', 'Company features updated successfully.');
    }

    private function setupWarnings(): array
    {
        $warnings = [];

        if (! Schema::hasTable('features')) {
            $warnings[] = 'Tabel features belum ada, daftar module belum bisa ditampilkan.';
        }

        if (! Schema::hasTable('company_features')) {
            $warnings[] = 'Tabel company_features belum ada, toggle fitur per company belum bisa disimpan.';
        }

        $missingColumns = $this->missingCompanyColumns();
        if ($missingColumns !== []) {
            $warnings[] = 'Kolom billing di tabel companies belum lengkap: '.implode(', ', $missingColumns).'.';
        }

        return $warnings;
    }

    private function missingCompanyColumns(): array
    {
        if (! Schema::hasTable('companies')) {
            return self::BILLING_COLUMNS;
        }

        return collect(self::BILLING_COLUMNS)
            ->reject(fn ($column) => Schema::hasColumn('companies', $column))
            ->values()
            ->all();
    }
}
