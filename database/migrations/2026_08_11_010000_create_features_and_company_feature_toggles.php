<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(false)->after('company_id');
            });
        }

        if (! Schema::hasTable('features')) {
            Schema::create('features', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('company_features')) {
            Schema::create('company_features', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->string('feature_key');
                $table->boolean('is_enabled')->default(true);
                $table->timestamps();
                $table->unique(['company_id', 'feature_key']);
            });
        }

        if (Schema::hasTable('permission_categories') && ! Schema::hasColumn('permission_categories', 'feature_key')) {
            Schema::table('permission_categories', function (Blueprint $table) {
                $table->string('feature_key')->nullable()->after('display_order');
            });
        }

        $this->seedFeaturesFromPermissionCategories();
        $this->enableAllFeaturesForExistingCompanies();
        $this->ensureDemoSuperAdmin();
    }

    public function down(): void
    {
        if (Schema::hasTable('permission_categories') && Schema::hasColumn('permission_categories', 'feature_key')) {
            Schema::table('permission_categories', function (Blueprint $table) {
                $table->dropColumn('feature_key');
            });
        }

        Schema::dropIfExists('company_features');
        Schema::dropIfExists('features');

        if (Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_super_admin');
            });
        }
    }

    private function seedFeaturesFromPermissionCategories(): void
    {
        if (! Schema::hasTable('permission_categories')) {
            return;
        }

        DB::table('permission_categories')->orderBy('display_order')->orderBy('id')->get()->each(function ($category) {
            $featureKey = $category->feature_key ?: Str::slug($category->name, '_');

            DB::table('permission_categories')
                ->where('id', $category->id)
                ->update(['feature_key' => $featureKey]);

            DB::table('features')->updateOrInsert(
                ['key' => $featureKey],
                [
                    'name' => $category->name,
                    'description' => 'Controls access to '.$category->name.' module.',
                    'display_order' => $category->display_order ?? 0,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        });
    }

    private function enableAllFeaturesForExistingCompanies(): void
    {
        if (! Schema::hasTable('companies')) {
            return;
        }

        $companies = DB::table('companies')->pluck('id');
        $features = DB::table('features')->pluck('key');

        foreach ($companies as $companyId) {
            foreach ($features as $featureKey) {
                DB::table('company_features')->updateOrInsert(
                    ['company_id' => $companyId, 'feature_key' => $featureKey],
                    ['is_enabled' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    private function ensureDemoSuperAdmin(): void
    {
        DB::table('users')
            ->where('email', 'admin@local.test')
            ->update(['is_super_admin' => true]);

        DB::table('users')
            ->where('email', 'test@example.com')
            ->orWhere('phone', '0881023806530')
            ->update(['is_super_admin' => false]);
    }
};
