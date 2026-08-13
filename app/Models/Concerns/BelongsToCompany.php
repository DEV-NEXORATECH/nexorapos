<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $table = $builder->getModel()->getTable();

            if (auth()->hasUser() && auth()->user()->isSuperAdmin()) {
                return;
            }

            $companyId = auth()->hasUser() ? auth()->user()->company_id : session('company_id');

            if ($companyId) {
                $builder->where($table . '.company_id', $companyId);
            }
        });

        static::creating(function (Model $model) {
            $isSuperAdmin = auth()->hasUser() ? auth()->user()->isSuperAdmin() : false;
            
            if (! $model->company_id && ! $isSuperAdmin) {
                $model->company_id = auth()->hasUser() ? auth()->user()->company_id : session('company_id');
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
