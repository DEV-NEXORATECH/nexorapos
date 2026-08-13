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
            if (! Schema::hasColumn('companies', 'billing_period')) {
                $table->string('billing_period')->default('monthly')->after('subscription_status');
            }

            if (! Schema::hasColumn('companies', 'billing_interval_months')) {
                $table->unsignedSmallInteger('billing_interval_months')->default(1)->after('billing_period');
            }

            if (! Schema::hasColumn('companies', 'payment_amount')) {
                $table->decimal('payment_amount', 12, 2)->nullable()->after('billing_interval_months');
            }

            if (! Schema::hasColumn('companies', 'last_paid_at')) {
                $table->date('last_paid_at')->nullable()->after('payment_amount');
            }
        });

        DB::table('companies')
            ->whereNull('billing_period')
            ->orWhere('billing_period', '')
            ->update([
                'billing_period' => 'monthly',
                'billing_interval_months' => 1,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            foreach (['last_paid_at', 'payment_amount', 'billing_interval_months', 'billing_period'] as $column) {
                if (Schema::hasColumn('companies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
