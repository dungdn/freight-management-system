<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Company access records.
     */
    public function companyAccess(): HasMany
    {
        return $this->hasMany(UserCompanyAccess::class);
    }

    /**
     * Branch access records.
     */
    public function branchAccess(): HasMany
    {
        return $this->hasMany(UserBranchAccess::class);
    }

    /**
     * Accessible companies.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_company_access')
            ->withPivot(['is_default', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Accessible branches.
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'user_branch_access')
            ->withPivot(['company_id', 'is_default', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Check if user has access to a specific company.
     */
    public function hasAccessToCompany(int $companyId): bool
    {
        return $this->companyAccess()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if user has access to a specific branch.
     */
    public function hasAccessToBranch(int $companyId, int $branchId): bool
    {
        return $this->branchAccess()
            ->where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->exists();
    }
}
