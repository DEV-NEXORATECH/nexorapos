<?php

namespace App\Http\Controllers\Admin;

use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Services\Admin\SettingService;
class SettingController extends Controller
{

   public $settingService;

   public function __construct()
   {
    $this->settingService=new SettingService();
   }

   public function index()
   {
    return $this->settingService->index();
   }

   public function add()
   {
    return $this->settingService->add();
   }

   public function store(Request $request)
   {
    return $this->settingService->store($request);
   }

   public function edit(Request $request, $id)
   {
    return $this->settingService->edit($request,$id);
   }

   public function update(Request $request, $id)
   {
    return $this->settingService->update($request,$id);
   }

   public function destroy(Request $request,$id)
   {
    return $this->settingService->destroy($request,$id);
   }

   public function license()
   {
    if (auth()->user()?->isSuperAdmin()) {
        return redirect('admin-panel/features')->with('failed', 'License Status hanya untuk user company. Super Admin mengatur paket dan fitur di Company Features.');
    }

    return $this->settingService->license();
   }

   public function company()
   {
    $company = auth()->user()?->company;

    if (! $company) {
        return redirect('admin-panel/dashboard')->with('failed', 'Akun belum terhubung ke company. Minta Super Admin assign company terlebih dahulu.');
    }

    return view('admin.setting.company', compact('company'));
   }

   public function updateCompany(Request $request)
   {
    $company = auth()->user()?->company;

    if (! $company) {
        return redirect('admin-panel/dashboard')->with('failed', 'Akun belum terhubung ke company. Minta Super Admin assign company terlebih dahulu.');
    }

    $missingColumns = collect(['legal_name', 'npwp', 'email', 'city', 'postal_code'])
        ->reject(fn ($column) => Schema::hasColumn('companies', $column))
        ->values()
        ->all();

    if ($missingColumns !== []) {
        return redirect()->back()->with('failed', 'Kolom company profile belum lengkap: '.implode(', ', $missingColumns).'. Jalankan migration company profile dulu.');
    }

    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'legal_name' => ['nullable', 'string', 'max:255'],
        'npwp' => ['nullable', 'string', 'max:40'],
        'email' => ['nullable', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:40'],
        'city' => ['nullable', 'string', 'max:100'],
        'postal_code' => ['nullable', 'string', 'max:20'],
        'address' => ['nullable', 'string', 'max:500'],
        'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ]);

    if ($request->hasFile('logo')) {
        $directory = public_path('admin/company_logos');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = 'company-'.$company->id.'-'.time().'.'.$request->file('logo')->getClientOriginalExtension();
        $request->file('logo')->move($directory, $filename);
        $data['logo'] = 'admin/company_logos/'.$filename;
    }

    $company->fill($data);
    $company->save();

    return redirect()->back()->with('success', 'Company profile updated successfully.');
   }
}
