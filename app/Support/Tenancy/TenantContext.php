<?php

namespace App\Support\Tenancy;

use App\Models\Branch;
use App\Models\Company;
use Closure;

class TenantContext
{
    protected static ?int $companyId = null;
    protected static ?int $branchId = null;
    protected static bool $tenancyEnabled = true;

    /**
     * Get the active company ID.
     */
    public static function getCompanyId(): ?int
    {
        if (!static::$tenancyEnabled) {
            return null;
        }

        if (static::$companyId !== null) {
            return static::$companyId;
        }

        if (session()->has('active_company_id')) {
            return (int) session()->get('active_company_id');
        }

        if (auth()->check()) {
            $user = auth()->user();
            // Default company from user access if available
            $defaultAccess = $user->companyAccess()->where('is_default', true)->first()
                ?? $user->companyAccess()->first();

            if ($defaultAccess) {
                static::$companyId = $defaultAccess->company_id;
                return static::$companyId;
            }
        }

        return null;
    }

    /**
     * Set the active company ID for current runtime.
     */
    public static function setCompanyId(?int $companyId): void
    {
        static::$companyId = $companyId;
        if (session()->isStarted()) {
            session()->put('active_company_id', $companyId);
        }
    }

    /**
     * Get the active branch ID.
     */
    public static function getBranchId(): ?int
    {
        if (!static::$tenancyEnabled) {
            return null;
        }

        if (static::$branchId !== null) {
            return static::$branchId;
        }

        if (session()->has('active_branch_id')) {
            return (int) session()->get('active_branch_id');
        }

        if (auth()->check() && static::getCompanyId()) {
            $user = auth()->user();
            $defaultBranchAccess = $user->branchAccess()
                ->where('company_id', static::getCompanyId())
                ->where('is_default', true)
                ->first()
                ?? $user->branchAccess()->where('company_id', static::getCompanyId())->first();

            if ($defaultBranchAccess) {
                static::$branchId = $defaultBranchAccess->branch_id;
                return static::$branchId;
            }
        }

        return null;
    }

    /**
     * Set the active branch ID for current runtime.
     */
    public static function setBranchId(?int $branchId): void
    {
        static::$branchId = $branchId;
        if (session()->isStarted()) {
            session()->put('active_branch_id', $branchId);
        }
    }

    /**
     * Set both company and branch context simultaneously.
     */
    public static function setContext(?int $companyId, ?int $branchId = null): void
    {
        static::setCompanyId($companyId);
        static::setBranchId($branchId);
    }

    /**
     * Clear the tenant context.
     */
    public static function clearContext(): void
    {
        static::$companyId = null;
        static::$branchId = null;
        if (session()->isStarted()) {
            session()->forget(['active_company_id', 'active_branch_id']);
        }
    }

    /**
     * Check if tenancy scoping is enabled.
     */
    public static function isTenancyEnabled(): bool
    {
        return static::$tenancyEnabled;
    }

    /**
     * Execute a callback with tenancy disabled (bypass company scope).
     * Useful for seeders, super admin tasks, cross-company reporting, or system cron jobs.
     */
    public static function withoutTenancy(Closure $callback): mixed
    {
        $previousState = static::$tenancyEnabled;
        static::$tenancyEnabled = false;

        try {
            return $callback();
        } finally {
            static::$tenancyEnabled = $previousState;
        }
    }
}
