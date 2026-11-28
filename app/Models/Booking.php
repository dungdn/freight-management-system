<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    use HasFactory, BelongsToCompany, BelongsToBranch;

    protected $fillable = [
        'company_id',
        'branch_id',
        'booking_number',
        'carrier_partner_id',
        'booking_reference',
        'status',
        'confirmed_at',
        'created_by',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'carrier_partner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipment::class, 'booking_shipment_links', 'booking_id', 'shipment_id')
            ->withPivot('linked_at');
    }
}
