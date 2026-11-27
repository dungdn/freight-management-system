<?php

namespace App\Services\Sales;

use App\Models\BusinessPartner;
use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Models\QuotationRevision;
use App\Models\SalesInquiry;
use App\Models\SalesOrder;
use App\Models\Shipment;
use App\Models\ShipmentParty;
use App\Services\Sequence\NumberingSequenceService;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class SalesToShipmentService
{
    public function __construct(
        protected NumberingSequenceService $sequenceService
    ) {}

    /**
     * Create a new Quotation with an initial Revision and Lines.
     */
    public function createQuotation(array $data, array $lines = []): Quotation
    {
        $companyId = $data['company_id'] ?? TenantContext::getCompanyId();
        $branchId = $data['branch_id'] ?? TenantContext::getBranchId();

        if (!$companyId) {
            throw new InvalidArgumentException('Company context is required to create a quotation.');
        }

        if (empty($data['business_partner_id'])) {
            throw new InvalidArgumentException('Business partner (Customer) is required.');
        }

        return DB::transaction(function () use ($data, $lines, $companyId, $branchId) {
            // Generate quotation number via sequence service (e.g. QT26-00001)
            $quotationNumber = $this->sequenceService->generate('QUOTATION', $branchId, $companyId);

            $quotation = Quotation::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'quotation_number' => $quotationNumber,
                'inquiry_id' => $data['inquiry_id'] ?? null,
                'business_partner_id' => $data['business_partner_id'],
                'service_type_id' => $data['service_type_id'] ?? null,
                'direction' => $data['direction'] ?? 'OUTBOUND',
                'transport_mode' => $data['transport_mode'] ?? 'OCEAN',
                'origin_location_id' => $data['origin_location_id'] ?? null,
                'destination_location_id' => $data['destination_location_id'] ?? null,
                'status' => $data['status'] ?? 'DRAFT',
                'current_revision_number' => 1,
                'valid_until' => $data['valid_until'] ?? now()->addDays(30),
                'remarks' => $data['remarks'] ?? null,
                'created_by' => $data['created_by'] ?? auth()->id(),
            ]);

            // Create initial Revision 1
            $revision = QuotationRevision::create([
                'quotation_id' => $quotation->id,
                'revision_number' => 1,
                'currency_id' => $data['currency_id'] ?? 1, // default USD / VND
                'currency_code' => $data['currency_code'] ?? null,
                'exchange_rate' => $data['exchange_rate'] ?? 1.0,
                'total_sell_amount' => 0,
                'total_cost_amount' => 0,
                'est_gross_profit' => 0,
                'terms_and_conditions' => $data['terms_and_conditions'] ?? 'Standard Freight Forwarding Terms & Conditions apply.',
                'revision_status' => 'DRAFT',
                'created_by' => $quotation->created_by,
            ]);

            // Add lines
            foreach ($lines as $line) {
                QuotationLine::create([
                    'quotation_revision_id' => $revision->id,
                    'charge_code_id' => $line['charge_code_id'] ?? null,
                    'description' => $line['description'] ?? 'Freight Charge',
                    'currency_id' => $line['currency_id'] ?? $revision->currency_id,
                    'unit' => $line['unit'] ?? 'UNIT',
                    'quantity' => $line['quantity'] ?? 1,
                    'unit_price' => $line['unit_price'] ?? 0,
                    'sell_amount' => ($line['quantity'] ?? 1) * ($line['unit_price'] ?? 0),
                    'cost_unit_price' => $line['cost_unit_price'] ?? 0,
                    'cost_amount' => ($line['quantity'] ?? 1) * ($line['cost_unit_price'] ?? 0),
                    'profit_amount' => (($line['quantity'] ?? 1) * ($line['unit_price'] ?? 0)) - (($line['quantity'] ?? 1) * ($line['cost_unit_price'] ?? 0)),
                    'remarks' => $line['remarks'] ?? null,
                ]);
            }

            // Recalculate totals
            $revision->recalculateTotals();

            // If linked to an inquiry, update inquiry status to QUOTED
            if ($quotation->inquiry_id) {
                SalesInquiry::where('id', $quotation->inquiry_id)
                    ->where('status', 'OPEN')
                    ->update(['status' => 'QUOTED']);
            }

            return $quotation->load(['revisions.lines', 'customer']);
        });
    }

    /**
     * Accept a Quotation and convert it into a Sales Order.
     * Idempotent: If already converted, returns the existing Sales Order.
     */
    public function acceptQuotation(int|Quotation $quotation, ?string $operationalInstructions = null): SalesOrder
    {
        $quote = $quotation instanceof Quotation ? $quotation : Quotation::findOrFail($quotation);

        // Check idempotency: if already has a Sales Order, return it
        $existingOrder = SalesOrder::where('quotation_id', $quote->id)->first();
        if ($existingOrder) {
            return $existingOrder;
        }

        return DB::transaction(function () use ($quote, $operationalInstructions) {
            $quote->refresh();

            // Load active revision
            $activeRevision = $quote->revisions()
                ->where('revision_number', $quote->current_revision_number)
                ->first() ?? $quote->latestRevision;

            if (!$activeRevision) {
                throw new RuntimeException("Quotation #{$quote->quotation_number} has no revisions to accept.");
            }

            // Mark quotation and revision as ACCEPTED
            $quote->update(['status' => 'ACCEPTED']);
            $activeRevision->update(['revision_status' => 'ACCEPTED']);

            // If quotation originated from inquiry, mark inquiry as WON
            if ($quote->inquiry_id) {
                SalesInquiry::where('id', $quote->inquiry_id)->update(['status' => 'WON']);
            }

            // Generate SO number (e.g. SO26-00001)
            $soNumber = $this->sequenceService->generate(
                'SALES_ORDER',
                $quote->branch_id,
                $quote->company_id
            );

            // Create Sales Order
            return SalesOrder::create([
                'company_id' => $quote->company_id,
                'branch_id' => $quote->branch_id,
                'sales_order_number' => $soNumber,
                'quotation_id' => $quote->id,
                'quotation_revision_id' => $activeRevision->id,
                'business_partner_id' => $quote->business_partner_id,
                'status' => 'ACCEPTED',
                'total_amount' => $activeRevision->total_sell_amount,
                'currency_id' => $activeRevision->currency_id,
                'accepted_at' => now(),
                'operational_instructions' => $operationalInstructions ?? $quote->remarks,
                'created_by' => auth()->id(),
            ]);
        });
    }

    /**
     * Convert an accepted Sales Order into an Operational Shipment.
     * Idempotent: If a shipment already exists for this sales order, returns it.
     */
    public function convertToShipment(int|SalesOrder $salesOrder, array $extraData = []): Shipment
    {
        $order = $salesOrder instanceof SalesOrder ? $salesOrder : SalesOrder::findOrFail($salesOrder);

        // Check idempotency: If shipment already exists for this order, return it immediately
        $existingShipment = Shipment::where('sales_order_id', $order->id)->first();
        if ($existingShipment) {
            return $existingShipment;
        }

        return DB::transaction(function () use ($order, $extraData) {
            $order->refresh();
            $quote = $order->quotation;

            // Generate Shipment Number (e.g. SH26-000001)
            $shipmentNumber = $this->sequenceService->generate(
                'SHIPMENT',
                $order->branch_id,
                $order->company_id
            );

            $direction = $extraData['direction'] ?? ($quote?->direction ?? 'OUTBOUND');
            $transportMode = $extraData['transport_mode'] ?? ($quote?->transport_mode ?? 'OCEAN');

            // Create Shipment
            $shipment = Shipment::create([
                'company_id' => $order->company_id,
                'branch_id' => $order->branch_id,
                'shipment_number' => $shipmentNumber,
                'sales_order_id' => $order->id,
                'quotation_id' => $order->quotation_id,
                'customer_id' => $order->business_partner_id,
                'direction' => $direction,
                'transport_mode' => $transportMode,
                'service_type_id' => $extraData['service_type_id'] ?? $quote?->service_type_id,
                'status' => 'DRAFT',
                'operational_status' => 'BOOKING_PENDING',
                'origin_location_id' => $extraData['origin_location_id'] ?? $quote?->origin_location_id,
                'destination_location_id' => $extraData['destination_location_id'] ?? $quote?->destination_location_id,
                'vessel_name' => $extraData['vessel_name'] ?? null,
                'voyage_no' => $extraData['voyage_no'] ?? null,
                'master_bl_no' => $extraData['master_bl_no'] ?? null,
                'house_bl_no' => $extraData['house_bl_no'] ?? null,
                'carrier_partner_id' => $extraData['carrier_partner_id'] ?? null,
                'etd' => $extraData['etd'] ?? null,
                'eta' => $extraData['eta'] ?? null,
                'opened_at' => now(),
                'created_by' => auth()->id(),
            ]);

            // Add default ShipmentParty snapshot for customer
            $partner = BusinessPartner::find($order->business_partner_id);
            if ($partner) {
                $role = ($direction === 'INBOUND') ? 'CONSIGNEE' : 'SHIPPER';
                $primaryContact = $partner->primaryContact()->first();
                $primaryAddress = $partner->primaryAddress()->first() ?? $partner->addresses()->first();

                ShipmentParty::create([
                    'shipment_id' => $shipment->id,
                    'party_role' => $role,
                    'business_partner_id' => $partner->id,
                    'name_snapshot' => $partner->legal_name,
                    'address_snapshot' => $primaryAddress ? ($primaryAddress->address_line1 . ', ' . $primaryAddress->city) : null,
                    'contact_snapshot' => $primaryContact ? ($primaryContact->contact_name . ' (' . $primaryContact->phone . ')') : null,
                    'created_at' => now(),
                ]);
            }

            // Update Sales Order status
            $order->update([
                'status' => 'CONVERTED_TO_SHIPMENT',
                'converted_at' => now(),
            ]);

            return $shipment;
        });
    }

    /**
     * Run the complete end-to-end pipeline in one call.
     */
    public function fullSalesToShipmentPipeline(array $quotationData, array $lines = [], array $extraShipmentData = []): array
    {
        return DB::transaction(function () use ($quotationData, $lines, $extraShipmentData) {
            $quotation = $this->createQuotation($quotationData, $lines);
            $salesOrder = $this->acceptQuotation($quotation);
            $shipment = $this->convertToShipment($salesOrder, $extraShipmentData);

            return [
                'quotation' => $quotation,
                'sales_order' => $salesOrder,
                'shipment' => $shipment,
            ];
        });
    }
}
