<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings') || Schema::hasColumn('settings', 'company_id')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            $table->index(['company_id', 'key']);
        });

        $companyId = DB::table('companies')->where('company_code', 'NEXORA')->value('id');

        if ($companyId) {
            DB::table('settings')->whereNull('company_id')->update(['company_id' => $companyId]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings') || ! Schema::hasColumn('settings', 'company_id')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'key']);
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
