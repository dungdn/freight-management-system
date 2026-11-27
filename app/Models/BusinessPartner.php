<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BusinessPartner extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'partner_code',
        'legal_name',
        'display_name',
        'partner_kind',
        'tax_code',
        'registration_no',
        'website',
        'default_currency_id',
        'payment_term_id',
        'status',
        'notes',
    ];

    public function defaultCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class, 'payment_term_id');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(PartnerRole::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(PartnerContact::class);
    }

    public function primaryContact(): HasOne
    {
        return $this->hasOne(PartnerContact::class)->where('is_primary', true);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(PartnerAddress::class);
    }

    public function primaryAddress(): HasOne
    {
        return $this->hasOne(PartnerAddress::class)->where('is_default', true);
    }

    public function creditProfiles(): HasMany
    {
        return $this->hasMany(PartnerCreditProfile::class);
    }

    public function activeCreditProfile(): HasOne
    {
        return $this->hasOne(PartnerCreditProfile::class)
            ->where('is_active', true)
            ->latestOfMany('effective_from');
    }

    public function salesAssignments(): HasMany
    {
        return $this->hasMany(PartnerSalesAssignment::class);
    }

    public function hasRole(string $roleCode): bool
    {
        return $this->roles()
            ->where('role_code', strtoupper($roleCode))
            ->where('is_active', true)
            ->exists();
    }
}
