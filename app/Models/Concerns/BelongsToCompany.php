<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $model = $builder->getModel();

            if (! Schema::hasColumn($model->getTable(), 'company_id')) {
                return;
            }

            if (auth()->user()?->isSuperAdmin()) {
                return;
            }

            $companyId = auth()->user()?->company_id ?? session('company_id');

            if ($companyId) {
                $builder->where($model->getTable().'.company_id', $companyId);
            }
        });

        static::creating(function (Model $model) {
            if (! Schema::hasColumn($model->getTable(), 'company_id')) {
                return;
            }

            if (! $model->company_id && ! auth()->user()?->isSuperAdmin()) {
                $model->company_id = auth()->user()?->company_id ?? session('company_id');
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
