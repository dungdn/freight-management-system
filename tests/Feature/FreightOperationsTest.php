<?php

use App\Livewire\Operations\ShipmentManager;
use App\Models\BusinessPartner;
use App\Models\Company;
use App\Models\ContainerType;
use App\Models\PackageType;
use App\Models\Shipment;
use App\Services\Operations\FreightOperationsService;
use App\Support\Tenancy\TenantContext;
use Livewire\Livewire;

beforeEach(function () {
    $this->company = Company::first();
    $this->branch = $this->company->branches()->first();
    TenantContext::setContext($this->company->id, $this->branch->id);

    $this->customer = BusinessPartner::where('company_id', $this->company->id)->first();
    $this->carrier = BusinessPartner::where('company_id', $this->company->id)
        ->whereHas('roles', fn ($q) => $q->where('role_code', 'CARRIER'))
        ->first() ?? $this->customer;

    $this->packageType = PackageType::first();
    $this->containerType = ContainerType::first();
    $this->service = app(FreightOperationsService::class);
});

afterEach(function () {
    TenantContext::clearContext();
});

test('freight operations service creates direct shipment with cargo items and initial event', function () {
    $shipment = $this->service->createDirectShipment([
        'customer_id' => $this->customer->id,
        'direction' => 'OUTBOUND',
        'transport_mode' => 'OCEAN',
        'carrier_partner_id' => $this->carrier->id,
        'vessel_name' => 'ONE HONOLULU',
        'voyage_no' => '2026W',
        'etd' => now()->addDays(5),
        'eta' => now()->addDays(20),
    ], [
        [
            'package_type_id' => $this->packageType?->id,
            'package_count' => 500,
            'gross_weight_kg' => 12500,
            'volume_cbm' => 45,
            'container_type_id' => $this->containerType?->id,
            'container_number' => 'ONEU9876543',
            'seal_number' => 'SL123456',
            'description' => 'Electronic Parts & Components',
        ]
    ]);

    expect($shipment)->toBeInstanceOf(Shipment::class)
        ->and($shipment->shipment_number)->toStartWith('SH26-')
        ->and($shipment->status)->toBe('ACTIVE')
        ->and($shipment->operational_status)->toBe('BOOKING_PENDING')
        ->and($shipment->cargoItems)->toHaveCount(1)
        ->and($shipment->events)->toHaveCount(1)
        ->and($shipment->events->first()->event_code)->toBe('JOB_OPENED');
});

test('recording milestone events advances shipment operational status', function () {
    $shipment = $this->service->createDirectShipment([
        'customer_id' => $this->customer->id,
        'direction' => 'OUTBOUND',
        'transport_mode' => 'OCEAN',
    ]);

    // 1. Confirm booking
    $this->service->recordMilestoneEvent($shipment, 'BOOKING_CONFIRMED', 'Carrier accepted slot');
    expect($shipment->fresh()->operational_status)->toBe('BOOKING_CONFIRMED');

    // 2. Cargo received
    $this->service->recordMilestoneEvent($shipment, 'CARGO_RECEIVED', 'Gate-in Cat Lai terminal');
    expect($shipment->fresh()->operational_status)->toBe('CARGO_RECEIVED');

    // 3. Departed
    $this->service->recordMilestoneEvent($shipment, 'DEPARTED', 'Vessel departed');
    $fresh = $shipment->fresh();
    expect($fresh->operational_status)->toBe('IN_TRANSIT')
        ->and($fresh->actual_departure)->not->toBeNull();

    // 4. Arrived
    $this->service->recordMilestoneEvent($shipment, 'ARRIVED', 'Vessel docked Los Angeles');
    expect($shipment->fresh()->operational_status)->toBe('ARRIVED');

    // 5. Delivered
    $this->service->recordMilestoneEvent($shipment, 'DELIVERED', 'Consignee received goods');
    $completed = $shipment->fresh();
    expect($completed->operational_status)->toBe('DELIVERED')
        ->and($completed->status)->toBe('COMPLETED')
        ->and($completed->completed_at)->not->toBeNull();
});

test('createCarrierBooking generates booking number and links to shipment', function () {
    $shipment = $this->service->createDirectShipment([
        'customer_id' => $this->customer->id,
        'direction' => 'OUTBOUND',
        'transport_mode' => 'OCEAN',
        'carrier_partner_id' => $this->carrier->id,
    ]);

    $booking = $this->service->createCarrierBooking($shipment, $this->carrier->id, 'BKG-ONE-888999');

    expect($booking->booking_number)->toStartWith('BK26-')
        ->and($booking->booking_reference)->toBe('BKG-ONE-888999')
        ->and($booking->status)->toBe('CONFIRMED');

    $linkedShipment = $booking->shipments()->first();
    expect($linkedShipment)->not->toBeNull()
        ->and($linkedShipment->id)->toBe($shipment->id)
        ->and($shipment->fresh()->operational_status)->toBe('BOOKING_CONFIRMED');
});

test('shipments workbench page renders successfully with livewire component', function () {
    $response = $this->get('/operations/shipments');

    $response->assertStatus(200);
    $response->assertSeeLivewire(ShipmentManager::class);
    $response->assertSee('Freight Shipments (Jobs)');
    $response->assertSee('New Shipment Job');
});

test('shipment manager creates a new job via livewire workbench', function () {
    Livewire::test(ShipmentManager::class)
        ->set('customer_id', $this->customer->id)
        ->set('transport_mode', 'OCEAN')
        ->set('direction', 'OUTBOUND')
        ->set('vessel_name', 'COSCO SHIPPING GEMINI')
        ->set('voyage_no', '024E')
        ->set('cargo_description', 'High-tech electronics')
        ->set('container_number', 'TGHU1234567')
        ->set('gross_weight_kg', '14000')
        ->call('createShipment')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false)
        ->assertSet('showDetailDrawer', true);

    $sh = Shipment::where('company_id', $this->company->id)->latest('id')->first();
    expect($sh)->not->toBeNull()
        ->and($sh->shipment_number)->toStartWith('SH26-')
        ->and($sh->vessel_name)->toBe('COSCO SHIPPING GEMINI')
        ->and($sh->cargoItems)->toHaveCount(1)
        ->and($sh->cargoItems->first()->container_number)->toBe('TGHU1234567');
});

test('shipment manager advances milestone through quick step action', function () {
    $shipment = $this->service->createDirectShipment([
        'customer_id' => $this->customer->id,
        'direction' => 'OUTBOUND',
        'transport_mode' => 'OCEAN',
    ]);

    expect($shipment->operational_status)->toBe('BOOKING_PENDING');

    Livewire::test(ShipmentManager::class)
        ->call('advanceMilestone', $shipment->id, 'BOOKING_CONFIRMED');

    expect($shipment->fresh()->operational_status)->toBe('BOOKING_CONFIRMED');
});
