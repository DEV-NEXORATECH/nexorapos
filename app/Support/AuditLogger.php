<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AuditLogger
{
    public static function log(string $action, string $module, ?Model $target = null, array $oldValue = [], array $newValue = []): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        $user = auth()->user();

        AuditLog::create([
            'actor_user_id' => $user?->id,
            'company_id' => $target?->company_id ?? $user?->company_id,
            'action' => $action,
            'module' => $module,
            'target_type' => $target ? $target::class : null,
            'target_id' => $target?->getKey(),
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => request()?->ip(),
            'user_agent' => substr((string) request()?->userAgent(), 0, 1000),
            'created_at' => now(),
        ]);
    }
}
