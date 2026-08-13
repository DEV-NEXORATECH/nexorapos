<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'category_id')) {
            return;
        }

        if ($this->categoryForeignKeyReferences() === 'product_categories') {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            // Drop old foreign key (if exists)
            if ($this->categoryForeignKeyReferences()) {
                $table->dropForeign(['category_id']);
            }

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
            if ($this->categoryForeignKeyReferences()) {
                $table->dropForeign(['category_id']);
            }
        });
    }

    private function categoryForeignKeyReferences(): ?string
    {
        if (DB::getDriverName() !== 'mysql') {
            return null;
        }

        $result = DB::selectOne(
            "select referenced_table_name as target_table from information_schema.key_column_usage where table_schema = database() and table_name = 'products' and column_name = 'category_id' and referenced_table_name is not null limit 1"
        );

        return $result?->target_table;
    }
};
