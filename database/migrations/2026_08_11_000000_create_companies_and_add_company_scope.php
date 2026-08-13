<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('company_code')->unique();
                $table->string('logo')->nullable();
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        $companyId = DB::table('companies')->where('company_code', 'NEXORA')->value('id');

        if (! $companyId) {
            $companyId = DB::table('companies')->insertGetId([
                'name' => 'Nexora Demo Company',
                'company_code' => 'NEXORA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($this->companyTables() as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'company_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            });

            DB::table($table)->whereNull('company_id')->update(['company_id' => $companyId]);
        }

        if (Schema::hasTable('methods')) {
            foreach (['Cash', 'QRIS', 'Transfer'] as $methodName) {
                $exists = DB::table('methods')
                    ->where('company_id', $companyId)
                    ->where('name', $methodName)
                    ->exists();

                if (! $exists) {
                    DB::table('methods')->insert([
                        'company_id' => $companyId,
                        'name' => $methodName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->companyTables()) as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'company_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->dropConstrainedForeignId('company_id');
            });
        }

        Schema::dropIfExists('companies');
    }

    private function companyTables(): array
    {
        return [
            'users',
            'roles',
            'settings',
            'permission_categories',
            'permissions',
            'stores',
            'product_categories',
            'taxes',
            'products',
            'suppliers',
            'customers',
            'purchases',
            'purchase_items',
            'product_variants',
            'stock_movements',
            'discounts',
            'sales',
            'sale_items',
            'methods',
            'payments',
        ];
    }
};
