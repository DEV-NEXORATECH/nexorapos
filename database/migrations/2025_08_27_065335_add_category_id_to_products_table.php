<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'tax_is') && ! Schema::hasColumn('products', 'tax_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('tax_id')->nullable()->after('tax_is')->constrained('taxes')->nullOnDelete();
            });

            DB::table('products')->whereNull('tax_id')->update(['tax_id' => DB::raw('tax_is')]);
        }

        if (Schema::hasColumn('products', 'category_id')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
           $table->foreignId('category_id')->nullable()->after('tax_id')->constrained('product_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
