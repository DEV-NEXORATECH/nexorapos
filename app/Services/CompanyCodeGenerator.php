<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Str;

class CompanyCodeGenerator
{
    /**
     * Generate a unique, immutable company code based on the company name.
     * Format: {COMPANY-NAME}-{NUMBER} (e.g., BU-WINNY-POS-001)
     *
     * @param string $companyName
     * @return string
     */
    public function generate(string $companyName): string
    {
        $prefix = $this->generatePrefix($companyName);
        $sequence = 1;

        do {
            $code = sprintf('%s-%03d', $prefix, $sequence);
            $sequence++;
        } while ($this->codeExists($code));

        return $code;
    }

    /**
     * Generate a prefix from the company name.
     *
     * @param string $name
     * @return string
     */
    private function generatePrefix(string $name): string
    {
        $prefix = strtoupper(Str::slug($name, '-'));
        $prefix = trim($prefix, '-');

        if ($prefix === '') {
            $prefix = 'COMPANY';
        }

        if (strlen($prefix) > 32) {
            $prefix = rtrim(substr($prefix, 0, 32), '-');
        }

        return $prefix ?: 'COMPANY';
    }

    /**
     * Check if a code already exists in the database.
     *
     * @param string $code
     * @return bool
     */
    private function codeExists(string $code): bool
    {
        return Company::where('company_code', $code)->exists();
    }
}
