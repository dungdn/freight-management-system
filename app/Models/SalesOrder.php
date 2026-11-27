<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SalesOrder extends Model
{
    use HasFactory, BelongsToCompany, BelongsToBranch;

    protected $fillable = [
        'company_id',
        'branch_id',
        'sales_order_number',
        'quotation_id',
        'quotation_revision_id',
        'business_partner_id',
        'status',
        'total_amount',
        'currency_id',
        'accepted_at',
        'converted_at',
        'operational_instructions',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:4',
        'accepted_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'business_partner_id');
    }

    public function businessPartner(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'business_partner_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function quotationRevision(): BelongsTo
    {
        return $this->belongsTo(QuotationRevision::class, 'quotation_revision_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class, 'sales_order_id');
    }
}
