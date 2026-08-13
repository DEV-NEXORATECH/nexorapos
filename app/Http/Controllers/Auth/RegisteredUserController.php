<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'company_code' => Str::upper((string) $request->input('company_code')),
        ]);

        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_code' => ['required', 'string', 'alpha_dash', 'max:30', 'unique:companies,company_code'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $company = Company::create([
            'name' => $request->company_name,
            'company_code' => $request->company_code,
            'phone' => $request->phone,
            'is_active' => true,
            'subscription_status' => 'active',
            'payment_due_at' => now()->addMonth()->toDateString(),
        ]);

        Feature::pluck('key')->each(function ($featureKey) use ($company) {
            CompanyFeature::create([
                'company_id' => $company->id,
                'feature_key' => $featureKey,
                'is_enabled' => true,
            ]);
        });

        $role = Role::withoutGlobalScopes()->firstOrCreate([
            'company_id' => $company->id,
            'role' => 'Admin',
        ]);

        $this->cloneDefaultAccessForCompany($company, $role);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_id' => $company->id,
            'role_id' => $role->id,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    private function cloneDefaultAccessForCompany(Company $company, Role $role): void
    {
        if (! Schema::hasTable('permission_categories') || ! Schema::hasTable('permissions')) {
            return;
        }

        $templateCompanyId = Company::where('company_code', 'NEXORA')->value('id')
            ?? Company::where('id', '!=', $company->id)->value('id');

        $templateCategories = DB::table('permission_categories')
            ->when(Schema::hasColumn('permission_categories', 'company_id') && $templateCompanyId, fn ($query) => $query->where('company_id', $templateCompanyId))
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        if ($templateCategories->isEmpty()) {
            return;
        }

        foreach ($templateCategories as $templateCategory) {
            $categoryPayload = collect((array) $templateCategory)
                ->except(['id', 'created_at', 'updated_at', 'company_id'])
                ->toArray();

            $categoryPayload['created_at'] = now();
            $categoryPayload['updated_at'] = now();

            if (Schema::hasColumn('permission_categories', 'company_id')) {
                $categoryPayload['company_id'] = $company->id;
            }

            $categoryId = DB::table('permission_categories')->insertGetId($categoryPayload);

            DB::table('permissions')
                ->where('permission_category_id', $templateCategory->id)
                ->get()
                ->each(function ($templatePermission) use ($categoryId, $company, $role) {
                    $permissionPayload = collect((array) $templatePermission)
                        ->except(['id', 'created_at', 'updated_at', 'company_id', 'permission_category_id'])
                        ->toArray();

                    $permissionPayload['permission_category_id'] = $categoryId;
                    $permissionPayload['created_at'] = now();
                    $permissionPayload['updated_at'] = now();

                    if (Schema::hasColumn('permissions', 'company_id')) {
                        $permissionPayload['company_id'] = $company->id;
                    }

                    $permissionId = DB::table('permissions')->insertGetId($permissionPayload);

                    if (Schema::hasTable('role_permissions')) {
                        DB::table('role_permissions')->updateOrInsert(
                            ['role_id' => $role->id, 'permission_id' => $permissionId],
                            ['created_at' => now(), 'updated_at' => now()]
                        );
                    }
                });
        }
    }
}
