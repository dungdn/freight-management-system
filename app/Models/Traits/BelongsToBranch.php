<?php

namespace App\Models\Traits;

use App\Models\Branch;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBranch
{
    /**
     * Boot the trait.
     */
    public static function bootBelongsToBranch(): void
    {
        static::creating(function ($model) {
            if (empty($model->branch_id) && TenantContext::isTenancyEnabled()) {
                $branchId = TenantContext::getBranchId();
                if ($branchId) {
                    $model->branch_id = $branchId;
                }
            }
        });
    }

    /**
     * Relationship to Branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Filter by a specific branch or current active branch.
     */
    public function scopeForBranch(Builder $query, ?int $branchId = null): Builder
    {
        $branchId = $branchId ?? TenantContext::getBranchId();

        return $branchId !== null
            ? $query->where($this->qualifyColumn('branch_id'), $branchId)
            : $query;
    }
}
