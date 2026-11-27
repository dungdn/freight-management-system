<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotationRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'revision_number',
        'currency_id',
        'currency_code',
        'exchange_rate',
        'total_sell_amount',
        'total_cost_amount',
        'est_gross_profit',
        'terms_and_conditions',
        'terms_snapshot',
        'revision_status',
        'created_by',
    ];

    protected $casts = [
        'revision_number' => 'integer',
        'exchange_rate' => 'decimal:8',
        'total_sell_amount' => 'decimal:4',
        'total_cost_amount' => 'decimal:4',
        'est_gross_profit' => 'decimal:4',
        'terms_snapshot' => 'array',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(QuotationLine::class, 'quotation_revision_id');
    }

    /**
     * Recalculate totals from lines.
     */
    public function recalculateTotals(): self
    {
        $this->total_sell_amount = (string) $this->lines()->sum('sell_amount');
        $this->total_cost_amount = (string) $this->lines()->sum('cost_amount');
        $this->est_gross_profit = (string) ($this->total_sell_amount - $this->total_cost_amount);
        $this->save();

        return $this;
    }
}
