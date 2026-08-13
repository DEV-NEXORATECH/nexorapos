<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('companies')) {
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'legal_name')) {
                $table->string('legal_name')->nullable()->after('logo');
            }

            if (! Schema::hasColumn('companies', 'npwp')) {
                $table->string('npwp', 40)->nullable()->after('legal_name');
            }

            if (! Schema::hasColumn('companies', 'email')) {
                $table->string('email')->nullable()->after('npwp');
            }

            if (! Schema::hasColumn('companies', 'city')) {
                $table->string('city', 100)->nullable()->after('phone');
            }

            if (! Schema::hasColumn('companies', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('city');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('companies')) {
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            foreach (['postal_code', 'city', 'email', 'npwp', 'legal_name'] as $column) {
                if (Schema::hasColumn('companies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
