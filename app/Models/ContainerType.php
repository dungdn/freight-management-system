<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContainerType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'iso_size_type',
        'teu_factor',
        'tare_weight_kg',
        'max_payload_kg',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'teu_factor' => 'decimal:2',
            'tare_weight_kg' => 'integer',
            'max_payload_kg' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
