<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'license_started_at')) {
                $table->date('license_started_at')->nullable()->after('last_paid_at');
            }

            if (! Schema::hasColumn('companies', 'grace_period_days')) {
                $table->unsignedSmallInteger('grace_period_days')->default(7)->after('payment_due_at');
            }
        });

        DB::table('companies')
            ->whereNull('license_started_at')
            ->update([
                'license_started_at' => DB::raw('COALESCE(last_paid_at, DATE(created_at))'),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            foreach (['grace_period_days', 'license_started_at'] as $column) {
                if (Schema::hasColumn('companies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
