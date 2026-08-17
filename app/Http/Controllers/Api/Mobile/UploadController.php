<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Pos\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function companyLogo(Request $request)
    {
        $data = $request->validate([
            'company_id' => [$request->user()->isSuperAdmin() ? 'required' : 'nullable', 'integer'],
            'logo' => ['required', 'image', 'max:2048'],
        ]);

        $company = $request->user()->isSuperAdmin()
            ? Company::findOrFail($data['company_id'])
            : Company::findOrFail($request->user()->company_id);

        $filename = $this->moveImage($request->file('logo'), 'admin/company_logos');
        $company->logo = $filename;
        $company->save();

        return $this->json(true, 'Logo company berhasil diupload.', [
            'path' => 'admin/company_logos/'.$filename,
            'url' => asset('admin/company_logos/'.$filename),
            'company' => $company->fresh(),
        ]);
    }

    public function userProfile(Request $request, int $id)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        $companyId = $this->companyId($request);
        $user = User::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->findOrFail($id);

        $filename = $this->moveImage($data['image'], 'admin/profile_images');
        $user->profile_image = $filename;
        $user->save();

        return $this->json(true, 'Profile image berhasil diupload.', [
            'path' => 'admin/profile_images/'.$filename,
            'url' => asset('admin/profile_images/'.$filename),
            'user' => $user->fresh(),
        ]);
    }

    public function productImage(Request $request, int $id)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:4096'],
        ]);

        if (! Schema::hasColumn('products', 'image')) {
            return $this->json(false, 'Migration product image belum dijalankan. Jalankan php artisan migrate.', null, 503);
        }

        $companyId = $this->companyId($request);
        $product = Product::where('company_id', $companyId)->findOrFail($id);
        $filename = $this->moveImage($data['image'], 'admin/product_images');
        $product->image = $filename;
        $product->save();

        return $this->json(true, 'Product image berhasil diupload.', [
            'path' => 'admin/product_images/'.$filename,
            'url' => asset('admin/product_images/'.$filename),
            'product' => $product->fresh(),
        ]);
    }

    private function moveImage($file, string $directory): string
    {
        $path = public_path($directory);
        if (! is_dir($path)) {
            mkdir($path, 0775, true);
        }

        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->move($path, $filename);

        return $filename;
    }

    private function companyId(Request $request): int
    {
        if ($request->user()->isSuperAdmin()) {
            return (int) ($request->query('company_id') ?: $request->input('company_id') ?: abort(422, 'company_id wajib untuk Super Admin.'));
        }

        return (int) $request->user()->company_id;
    }

    private function json(bool $success, string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(compact('success', 'message', 'data'), $status);
    }
}
