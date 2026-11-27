<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerAddress extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'business_partner_id',
        'address_type',
        'label',
        'contact_name',
        'phone',
        'address_line1',
        'address_line2',
        'ward',
        'district',
        'city',
        'region',
        'postal_code',
        'country_id',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'business_partner_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
