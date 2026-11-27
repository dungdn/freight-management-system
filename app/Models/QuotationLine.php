<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_revision_id',
        'charge_code_id',
        'description',
        'currency_id',
        'unit',
        'quantity',
        'unit_price',
        'sell_amount',
        'cost_unit_price',
        'cost_amount',
        'profit_amount',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'sell_amount' => 'decimal:4',
        'cost_unit_price' => 'decimal:4',
        'cost_amount' => 'decimal:4',
        'profit_amount' => 'decimal:4',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::saving(function ($line) {
            $qty = (float) $line->quantity;
            $sellPrice = (float) $line->unit_price;
            $costPrice = (float) $line->cost_unit_price;

            $line->sell_amount = $qty * $sellPrice;
            $line->cost_amount = $qty * $costPrice;
            $line->profit_amount = $line->sell_amount - $line->cost_amount;
        });
    }

    public function revision(): BelongsTo
    {
        return $this->belongsTo(QuotationRevision::class, 'quotation_revision_id');
    }

    public function chargeCode(): BelongsTo
    {
        return $this->belongsTo(ChargeCode::class, 'charge_code_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
