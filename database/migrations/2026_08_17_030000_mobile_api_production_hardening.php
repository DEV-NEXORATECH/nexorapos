<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mobile_api_tokens')) {
            Schema::table('mobile_api_tokens', function (Blueprint $table) {
                if (! Schema::hasColumn('mobile_api_tokens', 'refresh_token_hash')) {
                    $table->string('refresh_token_hash', 64)->nullable()->unique()->after('token_hash');
                }
                if (! Schema::hasColumn('mobile_api_tokens', 'refresh_expires_at')) {
                    $table->timestamp('refresh_expires_at')->nullable()->after('expires_at');
                }
            });
        }

        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('image')->nullable()->after('barcode');
            });
        }

        if (Schema::hasTable('held_bills') && ! Schema::hasColumn('held_bills', 'table_name')) {
            Schema::table('held_bills', function (Blueprint $table) {
                $table->string('table_name')->nullable()->after('bill_name');
            });
        }

        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (! Schema::hasColumn('sales', 'idempotency_key')) {
                    $table->string('idempotency_key')->nullable()->after('invoice_number');
                    $table->unique(['company_id', 'idempotency_key']);
                }
                if (! Schema::hasColumn('sales', 'void_reason')) {
                    $table->text('void_reason')->nullable()->after('cancelled_at');
                }
                if (! Schema::hasColumn('sales', 'voided_by')) {
                    $table->foreignId('voided_by')->nullable()->after('void_reason')->constrained('users')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (Schema::hasColumn('sales', 'voided_by')) {
                    $table->dropConstrainedForeignId('voided_by');
                }
                if (Schema::hasColumn('sales', 'void_reason')) {
                    $table->dropColumn('void_reason');
                }
                if (Schema::hasColumn('sales', 'idempotency_key')) {
                    $table->dropUnique(['company_id', 'idempotency_key']);
                    $table->dropColumn('idempotency_key');
                }
            });
        }

        if (Schema::hasTable('held_bills') && Schema::hasColumn('held_bills', 'table_name')) {
            Schema::table('held_bills', fn (Blueprint $table) => $table->dropColumn('table_name'));
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'image')) {
            Schema::table('products', fn (Blueprint $table) => $table->dropColumn('image'));
        }

        if (Schema::hasTable('mobile_api_tokens')) {
            Schema::table('mobile_api_tokens', function (Blueprint $table) {
                if (Schema::hasColumn('mobile_api_tokens', 'refresh_token_hash')) {
                    $table->dropColumn('refresh_token_hash');
                }
                if (Schema::hasColumn('mobile_api_tokens', 'refresh_expires_at')) {
                    $table->dropColumn('refresh_expires_at');
                }
            });
        }
    }
};
