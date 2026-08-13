<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->changeForeignKey('sales', 'store_id', 'stores');
        $this->changeForeignKey('sales', 'user_id', 'users');
        $this->changeForeignKey('sale_items', 'product_id', 'products');
        $this->changeForeignKey('payments', 'method_id', 'methods');
        $this->changeForeignKey('purchases', 'supplier_id', 'suppliers');
        $this->changeForeignKey('purchase_items', 'product_id', 'products');
        $this->changeForeignKey('stock_movements', 'store_id', 'stores');
        $this->changeForeignKey('stock_movements', 'product_id', 'products');
        $this->changeForeignKey('stock_movements', 'product_variant_id', 'product_variants');
    }

    public function down(): void
    {
        $this->changeForeignKey('sales', 'store_id', 'stores', 'cascade');
        $this->changeForeignKey('sales', 'user_id', 'users', 'cascade');
        $this->changeForeignKey('sale_items', 'product_id', 'products', 'cascade');
        $this->changeForeignKey('payments', 'method_id', 'methods', 'cascade');
        $this->changeForeignKey('purchases', 'supplier_id', 'suppliers', 'cascade');
        $this->changeForeignKey('purchase_items', 'product_id', 'products', 'cascade');
        $this->changeForeignKey('stock_movements', 'store_id', 'stores', 'cascade');
        $this->changeForeignKey('stock_movements', 'product_id', 'products', 'cascade');
        $this->changeForeignKey('stock_movements', 'product_variant_id', 'product_variants', 'cascade');
    }

    private function changeForeignKey(string $table, string $column, string $references, string $action = 'restrict'): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $this->dropForeignKey($table, $column);

        Schema::table($table, function (Blueprint $blueprint) use ($column, $references, $action) {
            $foreign = $blueprint->foreign($column)->references('id')->on($references);
            $action === 'restrict' ? $foreign->restrictOnDelete() : $foreign->cascadeOnDelete();
        });
    }

    private function dropForeignKey(string $table, string $column): void
    {
        $defaultName = $table.'_'.$column.'_foreign';

        if ($this->foreignKeyExists($table, $defaultName)) {
            Schema::table($table, function (Blueprint $blueprint) use ($defaultName) {
                $blueprint->dropForeign($defaultName);
            });

            return;
        }

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $actual = DB::selectOne(
            'select constraint_name as name from information_schema.key_column_usage
             where table_schema = database() and table_name = ? and column_name = ? and referenced_table_name is not null limit 1',
            [$table, $column]
        );

        if ($actual) {
            DB::statement('ALTER TABLE `'.$table.'` DROP FOREIGN KEY `'.$actual->name.'`');
        }
    }

    private function foreignKeyExists(string $table, string $name): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            return collect(Schema::getForeignKeys($table))->contains(fn ($fk) => ($fk['name'] ?? $fk['columns'][0] ?? '') === $name);
        }

        return (bool) DB::selectOne(
            'select constraint_name from information_schema.table_constraints
             where table_schema = database() and table_name = ? and constraint_name = ? and constraint_type = ?',
            [$table, $name, 'FOREIGN KEY']
        );
    }
};
