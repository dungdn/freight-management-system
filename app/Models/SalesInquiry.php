<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesInquiry extends Model
{
    use HasFactory, BelongsToCompany, BelongsToBranch;

    protected $fillable = [
        'company_id',
        'branch_id',
        'inquiry_number',
        'business_partner_id',
        'service_type_id',
        'direction',
        'transport_mode',
        'origin_location_id',
        'destination_location_id',
        'status',
        'owner_user_id',
        'requested_ship_date',
        'cargo_summary',
        'est_gross_weight_kg',
        'est_volume_cbm',
        'notes',
    ];

    protected $casts = [
        'requested_ship_date' => 'date',
        'est_gross_weight_kg' => 'decimal:4',
        'est_volume_cbm' => 'decimal:4',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'business_partner_id');
    }

    public function businessPartner(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'business_partner_id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    public function ownerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'inquiry_id');
    }
}
