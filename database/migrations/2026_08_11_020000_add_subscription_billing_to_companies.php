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
            if (! Schema::hasColumn('companies', 'subscription_status')) {
                $table->string('subscription_status')->default('active')->after('is_active');
            }

            if (! Schema::hasColumn('companies', 'payment_due_at')) {
                $table->date('payment_due_at')->nullable()->after('subscription_status');
            }

            if (! Schema::hasColumn('companies', 'blocked_at')) {
                $table->timestamp('blocked_at')->nullable()->after('payment_due_at');
            }

            if (! Schema::hasColumn('companies', 'block_reason')) {
                $table->string('block_reason')->nullable()->after('blocked_at');
            }
        });

        DB::table('companies')
            ->whereNull('payment_due_at')
            ->update([
                'subscription_status' => 'active',
                'payment_due_at' => now()->addMonth()->toDateString(),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            foreach (['block_reason', 'blocked_at', 'payment_due_at', 'subscription_status'] as $column) {
                if (Schema::hasColumn('companies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
