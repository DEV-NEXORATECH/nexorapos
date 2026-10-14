<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('subdomain', 63)->nullable()->unique()->after('company_code');
            $table->string('domain_status', 20)->default('pending')->after('subdomain');
            $table->text('domain_error')->nullable()->after('domain_status');
            $table->timestamp('domain_created_at')->nullable()->after('domain_error');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique(['subdomain']);
            $table->dropColumn(['subdomain', 'domain_status', 'domain_error', 'domain_created_at']);
        });
    }
};
