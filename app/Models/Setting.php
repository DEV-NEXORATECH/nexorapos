<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use BelongsToCompany;

    protected $fillable = ['key', 'value', 'company_id'];

    public static function cachedValue(string $key, mixed $default = null): mixed
    {
        $companyId = auth()->user()?->company_id ?? session('company_id') ?? 'global';

        return Cache::remember("settings.{$companyId}.{$key}", now()->addHour(), function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function forgetCachedValue(string $key): void
    {
        Cache::forget("settings.global.{$key}");

        if ($companyId = auth()->user()?->company_id ?? session('company_id')) {
            Cache::forget("settings.{$companyId}.{$key}");
        }
    }
}
