<?php

namespace App\Services\Operations;

use App\Models\Booking;
use App\Models\BusinessPartner;
use App\Models\Shipment;
use App\Models\ShipmentCargoItem;
use App\Models\ShipmentEvent;
use App\Models\ShipmentLeg;
use App\Models\ShipmentParty;
use App\Services\Sequence\NumberingSequenceService;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FreightOperationsService
{
    public function __construct(
        protected NumberingSequenceService $sequenceService
    ) {}

    /**
     * Create an operational shipment directly (without sales order).
     */
    public function createDirectShipment(array $data, array $cargoItems = [], array $legs = []): Shipment
    {
        $companyId = $data['company_id'] ?? TenantContext::getCompanyId();
        $branchId = $data['branch_id'] ?? TenantContext::getBranchId();

        if (!$companyId) {
            throw new InvalidArgumentException('Company context is required to create a shipment.');
        }

        return DB::transaction(function () use ($data, $cargoItems, $legs, $companyId, $branchId) {
            $shipmentNumber = $this->sequenceService->generate('SHIPMENT', $branchId, $companyId);

            $shipment = Shipment::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'shipment_number' => $shipmentNumber,
                'customer_id' => $data['customer_id'] ?? null,
                'direction' => $data['direction'] ?? 'OUTBOUND',
                'transport_mode' => $data['transport_mode'] ?? 'OCEAN',
                'service_type_id' => $data['service_type_id'] ?? null,
                'status' => 'ACTIVE',
                'operational_status' => 'BOOKING_PENDING',
                'origin_location_id' => $data['origin_location_id'] ?? null,
                'destination_location_id' => $data['destination_location_id'] ?? null,
                'vessel_name' => $data['vessel_name'] ?? null,
                'voyage_no' => $data['voyage_no'] ?? null,
                'master_bl_no' => $data['master_bl_no'] ?? null,
                'house_bl_no' => $data['house_bl_no'] ?? null,
                'carrier_partner_id' => $data['carrier_partner_id'] ?? null,
                'etd' => $data['etd'] ?? null,
                'eta' => $data['eta'] ?? null,
                'opened_at' => now(),
                'created_by' => auth()->id(),
            ]);

            // Add Shipper party if customer is set
            if (!empty($data['customer_id'])) {
                $customer = BusinessPartner::find($data['customer_id']);
                if ($customer) {
                    $role = ($shipment->direction === 'INBOUND') ? 'CONSIGNEE' : 'SHIPPER';
                    ShipmentParty::create([
                        'shipment_id' => $shipment->id,
                        'party_role' => $role,
                        'business_partner_id' => $customer->id,
                        'name_snapshot' => $customer->legal_name,
                        'created_at' => now(),
                    ]);
                }
            }

            // Add Cargo items
            foreach ($cargoItems as $item) {
                $this->addCargoItem($shipment, $item);
            }

            // Add Legs
            foreach ($legs as $leg) {
                $this->addRoutingLeg($shipment, $leg);
            }

            // Initial milestone event
            $this->recordMilestoneEvent($shipment, 'JOB_OPENED', 'Shipment job opened in operational workbench');

            return $shipment->load(['cargoItems', 'legs', 'parties', 'events']);
        });
    }

    /**
     * Record a milestone event and advance shipment operational status.
     */
    public function recordMilestoneEvent(
        int|Shipment $shipment,
        string $eventCode,
        ?string $notes = null,
        string $eventSource = 'MANUAL',
        ?string $actualAt = null
    ): ShipmentEvent {
        $sh = $shipment instanceof Shipment ? $shipment : Shipment::findOrFail($shipment);
        $time = $actualAt ? \Illuminate\Support\Carbon::parse($actualAt) : now();

        return DB::transaction(function () use ($sh, $eventCode, $notes, $eventSource, $time) {
            $event = ShipmentEvent::create([
                'shipment_id' => $sh->id,
                'event_code' => $eventCode,
                'planned_at' => null,
                'actual_at' => $time,
                'event_source' => $eventSource,
                'notes' => $notes,
                'recorded_by' => auth()->id(),
            ]);

            // Advance shipment operational status based on event
            $updates = [];
            switch (strtoupper($eventCode)) {
                case 'BOOKING_CONFIRMED':
                    $updates['operational_status'] = 'BOOKING_CONFIRMED';
                    $updates['status'] = 'ACTIVE';
                    break;
                case 'CARGO_RECEIVED':
                    $updates['operational_status'] = 'CARGO_RECEIVED';
                    $updates['status'] = 'ACTIVE';
                    break;
                case 'CUSTOMS_CLEARED':
                    $updates['operational_status'] = 'CUSTOMS_CLEARED';
                    break;
                case 'DEPARTED':
                    $updates['operational_status'] = 'IN_TRANSIT';
                    $updates['actual_departure'] = $time;
                    $updates['status'] = 'ACTIVE';
                    break;
                case 'ARRIVED':
                    $updates['operational_status'] = 'ARRIVED';
                    $updates['actual_arrival'] = $time;
                    break;
                case 'DELIVERED':
                    $updates['operational_status'] = 'DELIVERED';
                    $updates['status'] = 'COMPLETED';
                    $updates['completed_at'] = $time;
                    break;
                case 'JOB_CLOSED':
                    $updates['status'] = 'CLOSED';
                    $updates['closed_at'] = $time;
                    break;
            }

            if (!empty($updates)) {
                $sh->update($updates);
            }

            return $event;
        });
    }

    /**
     * Add a cargo item to a shipment.
     */
    public function addCargoItem(int|Shipment $shipment, array $data): ShipmentCargoItem
    {
        $shipmentId = $shipment instanceof Shipment ? $shipment->id : $shipment;

        return ShipmentCargoItem::create([
            'shipment_id' => $shipmentId,
            'package_type_id' => $data['package_type_id'] ?? null,
            'package_type_code' => $data['package_type_code'] ?? null,
            'package_count' => $data['package_count'] ?? 1,
            'gross_weight_kg' => $data['gross_weight_kg'] ?? 0,
            'volume_cbm' => $data['volume_cbm'] ?? 0,
            'container_type_id' => $data['container_type_id'] ?? null,
            'container_number' => $data['container_number'] ?? null,
            'seal_number' => $data['seal_number'] ?? null,
            'description' => $data['description'] ?? 'General Cargo',
            'marks_and_numbers' => $data['marks_and_numbers'] ?? null,
        ]);
    }

    /**
     * Add a routing leg to a shipment.
     */
    public function addRoutingLeg(int|Shipment $shipment, array $data): ShipmentLeg
    {
        $sh = $shipment instanceof Shipment ? $shipment : Shipment::findOrFail($shipment);

        $nextSeq = (int) $sh->legs()->max('leg_sequence') + 1;

        return ShipmentLeg::create([
            'shipment_id' => $sh->id,
            'leg_sequence' => $data['leg_sequence'] ?? $nextSeq,
            'transport_mode' => $data['transport_mode'] ?? $sh->transport_mode,
            'origin_location_id' => $data['origin_location_id'] ?? $sh->origin_location_id,
            'destination_location_id' => $data['destination_location_id'] ?? $sh->destination_location_id,
            'planned_departure' => $data['planned_departure'] ?? $sh->etd,
            'actual_departure' => $data['actual_departure'] ?? null,
            'planned_arrival' => $data['planned_arrival'] ?? $sh->eta,
            'actual_arrival' => $data['actual_arrival'] ?? null,
            'carrier_partner_id' => $data['carrier_partner_id'] ?? $sh->carrier_partner_id,
            'vessel_name' => $data['vessel_name'] ?? $sh->vessel_name,
            'voyage_no' => $data['voyage_no'] ?? $sh->voyage_no,
        ]);
    }

    /**
     * Create and link a carrier booking for the shipment.
     */
    public function createCarrierBooking(
        int|Shipment $shipment,
        ?int $carrierPartnerId = null,
        ?string $bookingReference = null
    ): Booking {
        $sh = $shipment instanceof Shipment ? $shipment : Shipment::findOrFail($shipment);
        $companyId = $sh->company_id;
        $branchId = $sh->branch_id;
        $carrierId = $carrierPartnerId ?? $sh->carrier_partner_id;

        return DB::transaction(function () use ($sh, $companyId, $branchId, $carrierId, $bookingReference) {
            $bookingNumber = $this->sequenceService->generate('BOOKING', $branchId, $companyId);

            $booking = Booking::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'booking_number' => $bookingNumber,
                'carrier_partner_id' => $carrierId,
                'booking_reference' => $bookingReference,
                'status' => 'CONFIRMED',
                'confirmed_at' => now(),
                'created_by' => auth()->id(),
            ]);

            $sh->bookings()->attach($booking->id, ['linked_at' => now()]);

            $this->recordMilestoneEvent(
                $sh,
                'BOOKING_CONFIRMED',
                "Carrier booking confirmed with ref: " . ($bookingReference ?? $bookingNumber)
            );

            return $booking;
        });
    }
}
