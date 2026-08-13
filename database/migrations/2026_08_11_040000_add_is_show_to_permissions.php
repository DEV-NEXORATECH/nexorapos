<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permissions') && ! Schema::hasColumn('permissions', 'is_show')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->boolean('is_show')->default(true)->after('route');
            });

            DB::table('permissions')->update(['is_show' => true]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permissions') && Schema::hasColumn('permissions', 'is_show')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropColumn('is_show');
            });
        }
    }
};
