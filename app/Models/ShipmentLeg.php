<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentLeg extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'leg_sequence',
        'transport_mode',
        'origin_location_id',
        'destination_location_id',
        'planned_departure',
        'actual_departure',
        'planned_arrival',
        'actual_arrival',
        'carrier_partner_id',
        'vessel_name',
        'voyage_no',
    ];

    protected $casts = [
        'leg_sequence' => 'integer',
        'planned_departure' => 'datetime',
        'actual_departure' => 'datetime',
        'planned_arrival' => 'datetime',
        'actual_arrival' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'carrier_partner_id');
    }
}
