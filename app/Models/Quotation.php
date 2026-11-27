<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    use HasFactory, BelongsToCompany, BelongsToBranch;

    protected $fillable = [
        'company_id',
        'branch_id',
        'quotation_number',
        'inquiry_id',
        'business_partner_id',
        'service_type_id',
        'direction',
        'transport_mode',
        'origin_location_id',
        'destination_location_id',
        'status',
        'current_revision_number',
        'valid_until',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'current_revision_number' => 'integer',
    ];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(SalesInquiry::class, 'inquiry_id');
    }

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(QuotationRevision::class, 'quotation_id')->orderBy('revision_number', 'asc');
    }

    public function latestRevision(): HasOne
    {
        return $this->hasOne(QuotationRevision::class, 'quotation_id')->latestOfMany('revision_number');
    }

    public function activeRevision(): HasOne
    {
        return $this->hasOne(QuotationRevision::class, 'quotation_id')
            ->where('revision_number', $this->current_revision_number);
    }

    public function salesOrder(): HasOne
    {
        return $this->hasOne(SalesOrder::class, 'quotation_id');
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class, 'quotation_id');
    }
}
