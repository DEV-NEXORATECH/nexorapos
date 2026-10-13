<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Role;
use App\Services\CompanyCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Database\Seeders\RolePermissionSeeder;

class CompanyController extends Controller
{
    protected CompanyCodeGenerator $codeGenerator;

    public function __construct(CompanyCodeGenerator $codeGenerator)
    {
        $this->codeGenerator = $codeGenerator;
    }

    public function index()
    {
        $companies = Company::withCount('users')->orderBy('id', 'desc')->get();
        return view('admin.companies.index', compact('companies'));
    }

    public function add()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'npwp' => 'nullable|string|max:40',
            'is_active' => 'required|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // License
            'max_users' => 'nullable|integer|min:1',
            'billing_period' => 'nullable|string',
            'license_started_at' => 'nullable|date',
            'payment_due_at' => 'nullable|date',
            // Admin
            'admin_name' => 'required_with:admin_email|string|max:255',
            'admin_email' => 'required_with:admin_name|email|unique:users,email',
            'admin_password' => 'required_with:admin_email|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->except(['logo', 'admin_name', 'admin_email', 'admin_password']);
            $data['company_code'] = $this->codeGenerator->generate($request->name);

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/companies'), $filename);
                $data['logo'] = 'uploads/companies/' . $filename;
            }

            $company = Company::create($data);

            // Create Initial Admin if provided
            if ($request->filled('admin_email')) {
                $role = Role::firstOrCreate(['role' => 'Admin', 'company_id' => $company->id]);
                User::create([
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                    'password' => Hash::make($request->admin_password),
                    'role_id' => $role->id,
                    'company_id' => $company->id,
                    'is_super_admin' => 0,
                ]);
            }

            // Pastikan role Admin company baru memiliki semua permission dasar.
            app(RolePermissionSeeder::class)->run();

            DB::commit();

            return redirect('admin-panel/companies/listing')->with('success', 'Company created successfully. Company Code: ' . $company->company_code);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create company: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $company = Company::with('users')->findOrFail($id);
        return view('admin.companies.show', compact('company'));
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'npwp' => 'nullable|string|max:40',
            'is_active' => 'required|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->except(['company_code', 'logo']); // Ignore company_code unconditionally

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/companies'), $filename);
                $data['logo'] = 'uploads/companies/' . $filename;
            }

            $company->update($data);

            return redirect('admin-panel/companies/listing')->with('success', 'Company updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update company: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();
            return redirect()->back()->with('success', 'Company deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete company: ' . $e->getMessage());
        }
    }
}
