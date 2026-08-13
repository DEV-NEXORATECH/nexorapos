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
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'max_users')) {
                $table->unsignedInteger('max_users')->default(10)->after('is_active');
            }
        });

        DB::table('companies')
            ->whereNull('max_users')
            ->orWhere('max_users', 0)
            ->update(['max_users' => 10]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('companies') || ! Schema::hasColumn('companies', 'max_users')) {
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('max_users');
        });
    }
};
