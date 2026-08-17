<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cashier_shifts')) {
            Schema::create('cashier_shifts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('shift_name')->nullable();
                $table->decimal('opening_cash', 12, 2)->default(0);
                $table->decimal('closing_cash', 12, 2)->nullable();
                $table->decimal('expected_cash', 12, 2)->default(0);
                $table->decimal('cash_difference', 12, 2)->default(0);
                $table->unsignedInteger('sales_count')->default(0);
                $table->decimal('sales_total', 12, 2)->default(0);
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->string('status')->default('open');
                $table->text('note')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'store_id', 'user_id', 'status']);
            });
        }

        if (! Schema::hasTable('held_bills')) {
            Schema::create('held_bills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->string('bill_name');
                $table->json('cart_snapshot');
                $table->json('totals_snapshot')->nullable();
                $table->string('status')->default('held');
                $table->timestamp('restored_at')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'store_id', 'user_id', 'status']);
            });
        }

        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'cashier_shift_id')) {
                $table->foreignId('cashier_shift_id')->nullable()->after('user_id')->constrained('cashier_shifts')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'cashier_shift_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropConstrainedForeignId('cashier_shift_id');
            });
        }

        Schema::dropIfExists('held_bills');
        Schema::dropIfExists('cashier_shifts');
    }
};
