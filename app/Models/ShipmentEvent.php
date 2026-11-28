<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'event_code',
        'planned_at',
        'actual_at',
        'event_source',
        'notes',
        'corrected_event_id',
        'recorded_by',
    ];

    protected $casts = [
        'planned_at' => 'datetime',
        'actual_at' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function correctionOf(): BelongsTo
    {
        return $this->belongsTo(ShipmentEvent::class, 'corrected_event_id');
    }
}
