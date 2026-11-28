<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory, BelongsToCompany, BelongsToBranch;

    protected $fillable = [
        'company_id',
        'branch_id',
        'shipment_number',
        'sales_order_id',
        'quotation_id',
        'customer_id',
        'direction',
        'transport_mode',
        'service_type_id',
        'status',
        'operational_status',
        'origin_location_id',
        'destination_location_id',
        'vessel_name',
        'voyage_no',
        'master_bl_no',
        'house_bl_no',
        'carrier_partner_id',
        'etd',
        'eta',
        'actual_departure',
        'actual_arrival',
        'opened_at',
        'completed_at',
        'closed_at',
        'created_by',
    ];

    protected $casts = [
        'etd' => 'datetime',
        'eta' => 'datetime',
        'actual_departure' => 'datetime',
        'actual_arrival' => 'datetime',
        'opened_at' => 'datetime',
        'completed_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'customer_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'carrier_partner_id');
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

    public function parties(): HasMany
    {
        return $this->hasMany(ShipmentParty::class, 'shipment_id');
    }

    public function cargoItems(): HasMany
    {
        return $this->hasMany(ShipmentCargoItem::class, 'shipment_id');
    }

    public function legs(): HasMany
    {
        return $this->hasMany(ShipmentLeg::class, 'shipment_id')->orderBy('leg_sequence');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class, 'shipment_id')->latest('id');
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_shipment_links', 'shipment_id', 'booking_id')
            ->withPivot('linked_at');
    }
}
