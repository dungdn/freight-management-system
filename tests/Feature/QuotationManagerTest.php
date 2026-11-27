<?php

use App\Livewire\Sales\QuotationManager;
use App\Models\BusinessPartner;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\Shipment;
use App\Support\Tenancy\TenantContext;
use Livewire\Livewire;

beforeEach(function () {
    $this->company = Company::first();
    $this->branch = $this->company->branches()->first();
    TenantContext::setContext($this->company->id, $this->branch->id);

    $this->partner = BusinessPartner::where('company_id', $this->company->id)->first();
    $this->usd = Currency::where('code', 'USD')->first();
});

afterEach(function () {
    TenantContext::clearContext();
});

test('quotations index page renders successfully with livewire component', function () {
    $response = $this->get('/sales/quotations');

    $response->assertStatus(200);
    $response->assertSeeLivewire(QuotationManager::class);
    $response->assertSee('Freight Quotations');
    $response->assertSee('New Quotation');
});

test('quotation manager creates quotation via livewire and calculates totals', function () {
    Livewire::test(QuotationManager::class)
        ->set('business_partner_id', $this->partner->id)
        ->set('direction', 'OUTBOUND')
        ->set('transport_mode', 'OCEAN')
        ->set('currency_id', $this->usd->id)
        ->set('valid_until', now()->addDays(15)->format('Y-m-d'))
        ->set('lines', [
            [
                'charge_code_id' => null,
                'description' => 'Ocean Freight 20GP',
                'unit' => 'CONT',
                'quantity' => 2,
                'unit_price' => 1100,
                'cost_unit_price' => 850,
            ],
            [
                'charge_code_id' => null,
                'description' => 'Bill of Lading Doc Fee',
                'unit' => 'BL',
                'quantity' => 1,
                'unit_price' => 50,
                'cost_unit_price' => 20,
            ]
        ])
        ->call('createQuotation')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false)
        ->assertSet('showDetailDrawer', true);

    $quote = Quotation::where('company_id', $this->company->id)->latest('id')->first();
    expect($quote)->not->toBeNull()
        ->and($quote->quotation_number)->toStartWith('QT26-')
        ->and($quote->business_partner_id)->toBe($this->partner->id);

    $rev = $quote->revisions->first();
    expect((float) $rev->total_sell_amount)->toBe(2250.0) // 2*1100 + 50
        ->and((float) $rev->total_cost_amount)->toBe(1720.0) // 2*850 + 20
        ->and((float) $rev->est_gross_profit)->toBe(530.0);
});

test('quotation manager can accept quote and convert to shipment seamlessly', function () {
    $component = Livewire::test(QuotationManager::class)
        ->set('business_partner_id', $this->partner->id)
        ->set('currency_id', $this->usd->id)
        ->set('valid_until', now()->addDays(10)->format('Y-m-d'))
        ->set('lines', [
            [
                'description' => 'Air Freight Express Delivery',
                'unit' => 'KGS',
                'quantity' => 200,
                'unit_price' => 6.0,
                'cost_unit_price' => 4.5,
            ]
        ])
        ->call('createQuotation');

    $quote = Quotation::where('company_id', $this->company->id)->latest('id')->first();

    // 1. Accept quote
    $component->call('acceptQuote', $quote->id);

    $so = SalesOrder::where('quotation_id', $quote->id)->first();
    expect($so)->not->toBeNull()
        ->and($so->sales_order_number)->toStartWith('SO26-')
        ->and($so->status)->toBe('ACCEPTED')
        ->and($quote->fresh()->status)->toBe('ACCEPTED');

    // 2. Convert Sales Order to Shipment
    $component->call('convertOrderToShipment', $so->id);

    $shipment = Shipment::where('sales_order_id', $so->id)->first();
    expect($shipment)->not->toBeNull()
        ->and($shipment->shipment_number)->toStartWith('SH26-')
        ->and($shipment->customer_id)->toBe($this->partner->id)
        ->and($so->fresh()->status)->toBe('CONVERTED_TO_SHIPMENT');
});

test('quickConvertQuotationToShipment completes the entire flow in one click', function () {
    $component = Livewire::test(QuotationManager::class)
        ->set('business_partner_id', $this->partner->id)
        ->set('currency_id', $this->usd->id)
        ->set('valid_until', now()->addDays(20)->format('Y-m-d'))
        ->set('lines', [
            [
                'description' => 'Trucking Hanoi to Haiphong Port',
                'unit' => 'TRIP',
                'quantity' => 1,
                'unit_price' => 400,
                'cost_unit_price' => 300,
            ]
        ])
        ->call('createQuotation');

    $quote = Quotation::where('company_id', $this->company->id)->latest('id')->first();

    $component->call('quickConvertQuotationToShipment', $quote->id);

    $quote->refresh();
    expect($quote->status)->toBe('ACCEPTED');

    $so = SalesOrder::where('quotation_id', $quote->id)->first();
    expect($so)->not->toBeNull()
        ->and($so->status)->toBe('CONVERTED_TO_SHIPMENT');

    $shipment = Shipment::where('sales_order_id', $so->id)->first();
    expect($shipment)->not->toBeNull()
        ->and($shipment->shipment_number)->toStartWith('SH26-');
});
