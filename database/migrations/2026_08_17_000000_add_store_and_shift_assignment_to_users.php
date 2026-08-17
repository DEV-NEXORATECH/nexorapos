<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'store_id')) {
                $table->foreignId('store_id')->nullable()->after('company_id')->constrained('stores')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'shift_name')) {
                $table->string('shift_name')->nullable()->after('store_id');
            }

            if (! Schema::hasColumn('users', 'shift_start')) {
                $table->time('shift_start')->nullable()->after('shift_name');
            }

            if (! Schema::hasColumn('users', 'shift_end')) {
                $table->time('shift_end')->nullable()->after('shift_start');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'store_id')) {
                $table->dropConstrainedForeignId('store_id');
            }

            foreach (['shift_name', 'shift_start', 'shift_end'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
