<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Company extends Model
{
    protected ?array $enabledFeatureKeysCache = null;

    protected $fillable = [
        'name',
        'company_code',
        'logo',
        'legal_name',
        'npwp',
        'email',
        'address',
        'phone',
        'city',
        'postal_code',
        'is_active',
        'max_users',
        'subscription_status',
        'billing_period',
        'billing_interval_months',
        'payment_amount',
        'last_paid_at',
        'license_started_at',
        'payment_due_at',
        'grace_period_days',
        'blocked_at',
        'block_reason',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_users' => 'integer',
        'payment_amount' => 'decimal:2',
        'last_paid_at' => 'date',
        'license_started_at' => 'date',
        'payment_due_at' => 'date',
        'grace_period_days' => 'integer',
        'blocked_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function features()
    {
        if (! Schema::hasTable('company_features')) {
            return $this->belongsToMany(Feature::class, 'company_features', 'company_id', 'feature_key', 'id', 'key')
                ->whereRaw('1 = 0');
        }

        return $this->belongsToMany(Feature::class, 'company_features', 'company_id', 'feature_key', 'id', 'key')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    public function hasFeature(string $featureKey): bool
    {
        if ($this->isSubscriptionBlocked()) {
            return false;
        }

        return in_array($featureKey, $this->enabledFeatureKeys(), true);
    }

    public function enabledFeaturesCount(): int
    {
        return count($this->enabledFeatureKeys());
    }

    public function hasAnyEnabledFeature(): bool
    {
        if ($this->isSubscriptionBlocked()) {
            return false;
        }

        return $this->enabledFeaturesCount() > 0;
    }

    public function isLicenseInactive(): bool
    {
        return $this->isLicenseLocked() || ! $this->hasAnyEnabledFeature();
    }

    public function isSubscriptionBlocked(): bool
    {
        return $this->isLicenseLocked();
    }

    public function isPaymentWarning(): bool
    {
        return in_array($this->licenseStatus(), ['EXPIRING_SOON', 'GRACE_PERIOD'], true);
    }

    public function paymentDaysRemaining(): ?int
    {
        return $this->licenseSnapshot()['remaining_days'];
    }

    public function licenseStatus(): string
    {
        return $this->licenseSnapshot()['status'];
    }

    public function isLicenseLocked(): bool
    {
        return in_array($this->licenseStatus(), ['EXPIRED', 'SUSPENDED', 'INACTIVE'], true);
    }

    public function licenseSnapshot(): array
    {
        $today = now()->startOfDay();
        $dueDate = $this->payment_due_at?->copy()->startOfDay();
        $graceDays = max((int) ($this->grace_period_days ?? 7), 0);
        $graceEnd = $dueDate?->copy()->addDays($graceDays);
        $remainingDays = $dueDate ? (int) $today->diffInDays($dueDate, false) : null;
        $remainingGraceDays = $graceEnd ? (int) $today->diffInDays($graceEnd, false) : null;

        $status = 'ACTIVE';

        if (($this->subscription_status ?? 'active') === 'inactive') {
            $status = 'INACTIVE';
        } elseif (! $this->is_active || $this->subscription_status === 'blocked') {
            $status = 'SUSPENDED';
        } elseif (! $dueDate) {
            $status = 'INACTIVE';
        } elseif ($remainingDays < 0) {
            $status = $remainingGraceDays >= 0 ? 'GRACE_PERIOD' : 'EXPIRED';
        } elseif ($remainingDays <= 7) {
            $status = 'EXPIRING_SOON';
        }

        $enabledFeatureCount = $this->enabledFeaturesCount();
        $totalFeatureCount = Schema::hasTable('features') ? Feature::count() : 0;

        return [
            'status' => $status,
            'label' => ucwords(strtolower(str_replace('_', ' ', $status))),
            'badge_class' => match ($status) {
                'ACTIVE' => 'success',
                'EXPIRING_SOON' => 'warning',
                'GRACE_PERIOD' => 'warning',
                default => 'danger',
            },
            'is_locked' => in_array($status, ['EXPIRED', 'SUSPENDED', 'INACTIVE'], true),
            'is_warning' => in_array($status, ['EXPIRING_SOON', 'GRACE_PERIOD'], true),
            'remaining_days' => $remainingDays,
            'remaining_grace_days' => $remainingGraceDays,
            'started_at' => $this->license_started_at,
            'expiry_date' => $dueDate,
            'grace_period_days' => $graceDays,
            'grace_ends_at' => $graceEnd,
            'payment_status' => ucfirst(str_replace('_', ' ', $this->subscription_status ?? 'active')),
            'package' => $this->billingPeriodLabel(),
            'payment_amount' => $this->payment_amount,
            'last_paid_at' => $this->last_paid_at,
            'active_features' => $status === 'SUSPENDED' || $status === 'EXPIRED' || $status === 'INACTIVE' ? 0 : $enabledFeatureCount,
            'total_features' => $totalFeatureCount,
            'current_users' => $this->activeUsersCount(),
            'max_users' => $this->max_users,
            'remaining_user_slots' => $this->remainingUserSlots(),
        ];
    }

    public function billingPeriodLabel(): string
    {
        return match ($this->billing_period) {
            'yearly' => 'Tahunan',
            'custom' => ($this->billing_interval_months ?: 1).' bulan',
            default => 'Bulanan',
        };
    }

    public function nextPaymentDueDate()
    {
        $months = max((int) ($this->billing_interval_months ?: 1), 1);
        $baseDate = $this->payment_due_at && $this->payment_due_at->isFuture()
            ? $this->payment_due_at->copy()
            : now()->startOfDay();

        return $baseDate->addMonthsNoOverflow($months);
    }

    public function activeUsersCount(): int
    {
        return $this->users()->where('is_super_admin', false)->count();
    }

    public function remainingUserSlots(): ?int
    {
        if (! $this->max_users) {
            return null;
        }

        return max($this->max_users - $this->activeUsersCount(), 0);
    }

    public function userLimitReached(): bool
    {
        return $this->max_users && $this->activeUsersCount() >= $this->max_users;
    }

    private function enabledFeatureKeys(): array
    {
        if ($this->enabledFeatureKeysCache !== null) {
            return $this->enabledFeatureKeysCache;
        }

        if (! Schema::hasTable('company_features')) {
            return $this->enabledFeatureKeysCache = [];
        }

        return $this->enabledFeatureKeysCache = CompanyFeature::where('company_id', $this->id)
            ->where('is_enabled', true)
            ->pluck('feature_key')
            ->all();
    }
    protected static function booted()
    {
        static::updating(function ($company) {
            if ($company->isDirty('company_code')) {
                // Revert any changes to company_code
                $company->company_code = $company->getOriginal('company_code');
            }
        });
    }
}
