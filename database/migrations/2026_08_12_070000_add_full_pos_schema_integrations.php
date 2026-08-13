<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'invoice_number')) {
                $table->string('invoice_number')->unique()->after('id')->nullable();
            }
            if (! Schema::hasColumn('sales', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('total');
            }
            if (! Schema::hasColumn('sales', 'change_amount')) {
                $table->decimal('change_amount', 10, 2)->default(0)->after('paid_amount');
            }
            if (! Schema::hasColumn('sales', 'payment_status')) {
                $table->string('payment_status')->default('UNPAID')->after('status');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (! Schema::hasColumn('purchases', 'purchase_number')) {
                $table->string('purchase_number')->unique()->after('id')->nullable();
            }
            if (! Schema::hasColumn('purchases', 'reference')) {
                $table->string('reference')->nullable()->after('supplier_id');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (! Schema::hasColumn('customers', 'customer_code')) {
                $table->string('customer_code')->unique()->after('id')->nullable();
            }
        });

        Schema::table('stores', function (Blueprint $table) {
            if (! Schema::hasColumn('stores', 'store_code')) {
                $table->string('store_code')->after('id')->nullable();
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('payments', 'transaction_type')) {
                $table->string('transaction_type')->default('sale')->after('reference');
            }
        });
        
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'minimum_stock')) {
                $table->integer('minimum_stock')->default(0)->after('stock_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'minimum_stock')) {
                $table->dropColumn('minimum_stock');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'transaction_type')) {
                $table->dropColumn('transaction_type');
            }
            if (Schema::hasColumn('payments', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });

        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'store_code')) {
                $table->dropColumn('store_code');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'customer_code')) {
                $table->dropColumn('customer_code');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'purchase_number')) {
                $table->dropColumn('purchase_number');
            }
            if (Schema::hasColumn('purchases', 'reference')) {
                $table->dropColumn('reference');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'invoice_number')) {
                $table->dropColumn('invoice_number');
            }
            if (Schema::hasColumn('sales', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
            if (Schema::hasColumn('sales', 'change_amount')) {
                $table->dropColumn('change_amount');
            }
            if (Schema::hasColumn('sales', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
