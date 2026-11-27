<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'credit_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'credit_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
