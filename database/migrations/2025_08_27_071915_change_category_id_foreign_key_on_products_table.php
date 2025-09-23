<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop old foreign key (if exists)
            $table->dropForeign(['category_id']);

            // Add new foreign key to product_categories table
            $table->foreign('category_id')
                  ->references('id')
                  ->on('product_categories')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert back to the old foreign key (if it was categories table)
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }
};
