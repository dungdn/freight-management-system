<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumberingSequence extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'branch_scope_id',
        'document_type',
        'period_key',
        'prefix',
        'next_number',
        'padding',
        'reset_policy',
    ];

    protected function casts(): array
    {
        return [
            'branch_scope_id' => 'integer',
            'next_number' => 'integer',
            'padding' => 'integer',
        ];
    }

    /**
     * Optional branch relationship when branch_scope_id > 0.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_scope_id');
    }

    /**
     * Check if sequence is company-wide (branch_scope_id = 0).
     */
    public function isCompanyWide(): bool
    {
        return (int) $this->branch_scope_id === 0;
    }
}
