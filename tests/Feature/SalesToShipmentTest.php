<?php

use App\Models\BusinessPartner;
use App\Models\ChargeCode;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Location;
use App\Models\Quotation;
use App\Models\SalesInquiry;
use App\Models\SalesOrder;
use App\Models\ServiceType;
use App\Models\Shipment;
use App\Services\Sales\SalesToShipmentService;
use App\Services\Sequence\NumberingSequenceService;
use App\Support\Tenancy\TenantContext;

beforeEach(function () {
    // Reset tenant context to default company
    $this->company = Company::first();
    $this->branch = $this->company->branches()->first();

    TenantContext::setContext($this->company->id, $this->branch->id);

    $this->partner = BusinessPartner::where('company_id', $this->company->id)->first();
    $this->usd = Currency::where('code', 'USD')->first();
    $this->vnd = Currency::where('code', 'VND')->first();
    $this->serviceType = ServiceType::first();
    $this->location = Location::first();

    $this->service = app(SalesToShipmentService::class);
});

afterEach(function () {
    TenantContext::clearContext();
});

test('can create a quotation with lines and calculate totals correctly', function () {
    $chargeCode = ChargeCode::where('company_id', $this->company->id)->first();

    $quote = $this->service->createQuotation([
        'business_partner_id' => $this->partner->id,
        'direction' => 'OUTBOUND',
        'transport_mode' => 'OCEAN',
        'service_type_id' => $this->serviceType?->id,
        'currency_id' => $this->usd->id,
        'remarks' => 'Test quotation for electronics export',
    ], [
        [
            'charge_code_id' => $chargeCode?->id,
            'description' => 'Ocean Freight 40HC',
            'quantity' => 2,
            'unit_price' => 1500,
            'cost_unit_price' => 1200,
            'unit' => 'CONT',
        ],
        [
            'charge_code_id' => null,
            'description' => 'Terminal Handling Charge',
            'quantity' => 2,
            'unit_price' => 150,
            'cost_unit_price' => 130,
            'unit' => 'CONT',
        ],
    ]);

    expect($quote)->toBeInstanceOf(Quotation::class)
        ->and($quote->quotation_number)->toStartWith('QT26-')
        ->and($quote->status)->toBe('DRAFT')
        ->and($quote->company_id)->toBe($this->company->id)
        ->and($quote->branch_id)->toBe($this->branch->id);

    $revision = $quote->revisions->first();
    expect($revision)->not->toBeNull()
        ->and((float) $revision->total_sell_amount)->toBe(3300.0) // 2*1500 + 2*150
        ->and((float) $revision->total_cost_amount)->toBe(2660.0) // 2*1200 + 2*130
        ->and((float) $revision->est_gross_profit)->toBe(640.0)
        ->and($revision->lines)->toHaveCount(2);
});

test('acceptQuotation creates a sales order and updates status idempotently', function () {
    // 1. Create quote
    $quote = $this->service->createQuotation([
        'business_partner_id' => $this->partner->id,
        'currency_id' => $this->usd->id,
    ], [
        [
            'description' => 'Air Freight Express',
            'quantity' => 100,
            'unit_price' => 5.5,
            'cost_unit_price' => 4.2,
            'unit' => 'KGS',
        ]
    ]);

    // 2. Accept quotation
    $so1 = $this->service->acceptQuotation($quote, 'Deliver before Friday');

    expect($so1)->toBeInstanceOf(SalesOrder::class)
        ->and($so1->sales_order_number)->toStartWith('SO26-')
        ->and($so1->status)->toBe('ACCEPTED')
        ->and((float) $so1->total_amount)->toBe(550.0)
        ->and($quote->fresh()->status)->toBe('ACCEPTED');

    // 3. Test idempotency: Calling acceptQuotation again returns the same SalesOrder
    $so2 = $this->service->acceptQuotation($quote);

    expect($so2->id)->toBe($so1->id)
        ->and(SalesOrder::where('quotation_id', $quote->id)->count())->toBe(1);
});

test('convertToShipment generates a shipment with parties and updates sales order idempotently', function () {
    // 1. Create & Accept Quote
    $quote = $this->service->createQuotation([
        'business_partner_id' => $this->partner->id,
        'direction' => 'OUTBOUND',
        'transport_mode' => 'OCEAN',
        'currency_id' => $this->usd->id,
    ], [
        ['description' => 'Ocean Freight', 'quantity' => 1, 'unit_price' => 2000, 'cost_unit_price' => 1600]
    ]);

    $so = $this->service->acceptQuotation($quote);

    // 2. Convert to Shipment
    $shipment1 = $this->service->convertToShipment($so, [
        'vessel_name' => 'EVER GIVEN',
        'voyage_no' => '0124W',
    ]);

    expect($shipment1)->toBeInstanceOf(Shipment::class)
        ->and($shipment1->shipment_number)->toStartWith('SH26-')
        ->and($shipment1->vessel_name)->toBe('EVER GIVEN')
        ->and($shipment1->status)->toBe('DRAFT')
        ->and($shipment1->operational_status)->toBe('BOOKING_PENDING')
        ->and($shipment1->parties)->not->toBeEmpty();

    // Check party snapshot
    $shipperParty = $shipment1->parties->where('party_role', 'SHIPPER')->first();
    expect($shipperParty)->not->toBeNull()
        ->and($shipperParty->name_snapshot)->toBe($this->partner->legal_name);

    // Check sales order updated
    expect($so->fresh()->status)->toBe('CONVERTED_TO_SHIPMENT')
        ->and($so->fresh()->converted_at)->not->toBeNull();

    // 3. Test idempotency: Converting again returns the existing shipment without creating a duplicate
    $shipment2 = $this->service->convertToShipment($so);

    expect($shipment2->id)->toBe($shipment1->id)
        ->and(Shipment::where('sales_order_id', $so->id)->count())->toBe(1);
});

test('fullSalesToShipmentPipeline executes all three stages in one transaction', function () {
    $pipelineResult = $this->service->fullSalesToShipmentPipeline(
        [
            'business_partner_id' => $this->partner->id,
            'direction' => 'OUTBOUND',
            'transport_mode' => 'AIR',
            'currency_id' => $this->usd->id,
        ],
        [
            ['description' => 'General Air Cargo', 'quantity' => 500, 'unit_price' => 4.0, 'cost_unit_price' => 3.2]
        ],
        [
            'master_bl_no' => '080-12345678',
        ]
    );

    expect($pipelineResult)->toHaveKeys(['quotation', 'sales_order', 'shipment']);

    $quote = $pipelineResult['quotation'];
    $so = $pipelineResult['sales_order'];
    $shipment = $pipelineResult['shipment'];

    expect($quote->quotation_number)->toStartWith('QT26-')
        ->and($so->sales_order_number)->toStartWith('SO26-')
        ->and($shipment->shipment_number)->toStartWith('SH26-')
        ->and($shipment->master_bl_no)->toBe('080-12345678')
        ->and($shipment->sales_order_id)->toBe($so->id)
        ->and($so->quotation_id)->toBe($quote->id);
});
