<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use RuntimeException;

class QrisTestSeeder extends Seeder
{
    public function run(): void
    {
        $companyCode = env('QRIS_TEST_COMPANY_CODE');
        $company = $companyCode
            ? Company::where('company_code', $companyCode)->first()
            : Company::latest('id')->first();

        if (! $company) {
            throw new RuntimeException('Company untuk testing QRIS belum ditemukan. Isi QRIS_TEST_COMPANY_CODE di .env.');
        }

        $company->forceFill([
            'payment_amount' => 50,
        ])->save();

        $this->command?->info('Nominal QRIS testing untuk '.$company->name.' diatur menjadi Rp50.');
        $this->command?->info('Company Code: '.$company->company_code);
    }
}
