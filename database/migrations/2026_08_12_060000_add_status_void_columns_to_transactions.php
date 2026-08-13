<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'status')) {
                $table->string('status')->default('active')->after('reference');
            }

            if (! Schema::hasColumn('payments', 'voided_at')) {
                $table->timestamp('voided_at')->nullable()->after('status');
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (! Schema::hasColumn('stock_movements', 'status')) {
                $table->string('status')->default('active')->after('type');
            }

            if (! Schema::hasColumn('stock_movements', 'voided_at')) {
                $table->timestamp('voided_at')->nullable()->after('status');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('status');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (! Schema::hasColumn('purchases', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            foreach (['voided_at', 'status'] as $column) {
                if (Schema::hasColumn('stock_movements', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            foreach (['voided_at', 'status'] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
