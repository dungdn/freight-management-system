<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentCargoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'package_type_id',
        'package_type_code',
        'package_count',
        'gross_weight_kg',
        'volume_cbm',
        'container_type_id',
        'container_number',
        'seal_number',
        'description',
        'marks_and_numbers',
    ];

    protected $casts = [
        'package_count' => 'decimal:4',
        'gross_weight_kg' => 'decimal:4',
        'volume_cbm' => 'decimal:4',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    public function packageType(): BelongsTo
    {
        return $this->belongsTo(PackageType::class, 'package_type_id');
    }

    public function containerType(): BelongsTo
    {
        return $this->belongsTo(ContainerType::class, 'container_type_id');
    }
}
