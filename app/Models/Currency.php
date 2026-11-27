<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'minor_unit',
        'symbol',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'minor_unit' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
