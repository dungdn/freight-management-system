<?php

namespace App\Livewire\Operations;

use App\Models\BusinessPartner;
use App\Models\Company;
use App\Models\ContainerType;
use App\Models\Location;
use App\Models\PackageType;
use App\Models\ServiceType;
use App\Models\Shipment;
use App\Services\Operations\FreightOperationsService;
use App\Support\Tenancy\TenantContext;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ShipmentManager extends Component
{
    use WithPagination;

    // Filters & Pagination
    public string $search = '';
    public string $selectedStatus = 'ALL';
    public string $selectedMode = 'ALL';
    public string $selectedDirection = 'ALL';
    public int $perPage = 10;

    // Modals & Drawers
    public bool $showCreateModal = false;
    public bool $showDetailDrawer = false;
    public bool $showEventModal = false;
    public ?int $detailShipmentId = null;

    // Feedback
    public ?string $feedbackMessage = null;
    public string $feedbackType = 'success';

    // Create form fields
    public $customer_id = null;
    public string $direction = 'OUTBOUND';
    public string $transport_mode = 'OCEAN';
    public $service_type_id = null;
    public $origin_location_id = null;
    public $destination_location_id = null;
    public $carrier_partner_id = null;
    public string $vessel_name = '';
    public string $voyage_no = '';
    public string $master_bl_no = '';
    public string $house_bl_no = '';
    public string $etd = '';
    public string $eta = '';

    // Cargo item fields for create form
    public $package_type_id = null;
    public string $package_count = '1';
    public string $gross_weight_kg = '';
    public string $volume_cbm = '';
    public $container_type_id = null;
    public string $container_number = '';
    public string $seal_number = '';
    public string $cargo_description = '';

    // Event recording fields
    public string $event_code = 'BOOKING_CONFIRMED';
    public string $event_notes = '';
    public string $event_actual_at = '';

    public function mount(): void
    {
        if (!TenantContext::getCompanyId()) {
            $defaultCompanyId = Company::where('is_active', true)->value('id');
            if ($defaultCompanyId) {
                TenantContext::setCompanyId($defaultCompanyId);
            }
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedMode(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedDirection(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->customer_id = null;
        $this->direction = 'OUTBOUND';
        $this->transport_mode = 'OCEAN';
        $this->service_type_id = null;
        $this->origin_location_id = null;
        $this->destination_location_id = null;
        $this->carrier_partner_id = null;
        $this->vessel_name = '';
        $this->voyage_no = '';
        $this->master_bl_no = '';
        $this->house_bl_no = '';
        $this->etd = now()->addDays(3)->format('Y-m-d\TH:i');
        $this->eta = now()->addDays(15)->format('Y-m-d\TH:i');

        $this->package_type_id = null;
        $this->package_count = '1';
        $this->gross_weight_kg = '';
        $this->volume_cbm = '';
        $this->container_type_id = null;
        $this->container_number = '';
        $this->seal_number = '';
        $this->cargo_description = 'General Commercial Cargo';

        $this->showCreateModal = true;
    }

    public function closeCreate(): void
    {
        $this->showCreateModal = false;
    }

    public function openDetail(int $id): void
    {
        $this->detailShipmentId = $id;
        $this->showDetailDrawer = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailDrawer = false;
        $this->detailShipmentId = null;
    }

    public function openEventModal(int $id): void
    {
        $this->detailShipmentId = $id;
        $this->event_code = 'BOOKING_CONFIRMED';
        $this->event_notes = '';
        $this->event_actual_at = now()->format('Y-m-d\TH:i');
        $this->showEventModal = true;
    }

    public function closeEventModal(): void
    {
        $this->showEventModal = false;
    }

    public function createShipment(): void
    {
        $this->validate([
            'customer_id' => 'required',
            'transport_mode' => 'required',
            'direction' => 'required',
        ]);

        $customerId = !empty($this->customer_id) ? (int) $this->customer_id : null;
        $serviceTypeId = !empty($this->service_type_id) ? (int) $this->service_type_id : null;
        $carrierId = !empty($this->carrier_partner_id) ? (int) $this->carrier_partner_id : null;
        $originLocId = !empty($this->origin_location_id) ? (int) $this->origin_location_id : null;
        $destLocId = !empty($this->destination_location_id) ? (int) $this->destination_location_id : null;

        $cargoItems = [];
        if (!empty($this->cargo_description)) {
            $cargoItems[] = [
                'package_type_id' => !empty($this->package_type_id) ? (int) $this->package_type_id : null,
                'package_count' => (float) ($this->package_count ?: 1),
                'gross_weight_kg' => (float) ($this->gross_weight_kg ?: 0),
                'volume_cbm' => (float) ($this->volume_cbm ?: 0),
                'container_type_id' => !empty($this->container_type_id) ? (int) $this->container_type_id : null,
                'container_number' => $this->container_number ?: null,
                'seal_number' => $this->seal_number ?: null,
                'description' => $this->cargo_description,
            ];
        }

        $service = app(FreightOperationsService::class);
        $shipment = $service->createDirectShipment([
            'customer_id' => $customerId,
            'direction' => $this->direction,
            'transport_mode' => $this->transport_mode,
            'service_type_id' => $serviceTypeId,
            'origin_location_id' => $originLocId,
            'destination_location_id' => $destLocId,
            'carrier_partner_id' => $carrierId,
            'vessel_name' => $this->vessel_name ?: null,
            'voyage_no' => $this->voyage_no ?: null,
            'master_bl_no' => $this->master_bl_no ?: null,
            'house_bl_no' => $this->house_bl_no ?: null,
            'etd' => $this->etd ?: null,
            'eta' => $this->eta ?: null,
        ], $cargoItems);

        $this->showCreateModal = false;
        $this->feedbackType = 'success';
        $this->feedbackMessage = "Shipment #{$shipment->shipment_number} created successfully!";

        $this->openDetail($shipment->id);
    }

    public function advanceMilestone(int $shipmentId, string $eventCode): void
    {
        try {
            $service = app(FreightOperationsService::class);
            $service->recordMilestoneEvent(
                $shipmentId,
                $eventCode,
                "Milestone advanced to {$eventCode} via Operational Workbench."
            );

            $this->feedbackType = 'success';
            $this->feedbackMessage = "Shipment status successfully advanced: {$eventCode}!";
        } catch (\Throwable $e) {
            $this->feedbackType = 'error';
            $this->feedbackMessage = $e->getMessage();
        }
    }

    public function recordEvent(): void
    {
        if (!$this->detailShipmentId) {
            return;
        }

        try {
            $service = app(FreightOperationsService::class);
            $service->recordMilestoneEvent(
                $this->detailShipmentId,
                $this->event_code,
                $this->event_notes ?: null,
                'MANUAL',
                $this->event_actual_at ?: null
            );

            $this->showEventModal = false;
            $this->feedbackType = 'success';
            $this->feedbackMessage = "Milestone event '{$this->event_code}' recorded successfully!";
        } catch (\Throwable $e) {
            $this->feedbackType = 'error';
            $this->feedbackMessage = $e->getMessage();
        }
    }

    #[Computed]
    public function shipments()
    {
        $query = Shipment::query()
            ->with(['customer', 'carrier', 'serviceType', 'originLocation', 'destinationLocation', 'cargoItems', 'events'])
            ->latest('id');

        if (!empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('shipment_number', 'like', $term)
                    ->orWhere('master_bl_no', 'like', $term)
                    ->orWhere('house_bl_no', 'like', $term)
                    ->orWhere('vessel_name', 'like', $term)
                    ->orWhereHas('customer', function ($sub) use ($term) {
                        $sub->where('legal_name', 'like', $term);
                    });
            });
        }

        if ($this->selectedStatus !== 'ALL') {
            $query->where('operational_status', $this->selectedStatus);
        }

        if ($this->selectedMode !== 'ALL') {
            $query->where('transport_mode', $this->selectedMode);
        }

        if ($this->selectedDirection !== 'ALL') {
            $query->where('direction', $this->selectedDirection);
        }

        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function detailShipment(): ?Shipment
    {
        if (!$this->detailShipmentId) {
            return null;
        }

        return Shipment::with([
            'customer',
            'carrier',
            'serviceType',
            'originLocation',
            'destinationLocation',
            'parties.partner',
            'cargoItems.packageType',
            'cargoItems.containerType',
            'legs.carrier',
            'legs.originLocation',
            'legs.destinationLocation',
            'events.recorder',
            'salesOrder.quotation',
            'bookings',
        ])->find($this->detailShipmentId);
    }

    #[Computed]
    public function customers()
    {
        return BusinessPartner::where('status', 'ACTIVE')->orderBy('legal_name')->get();
    }

    #[Computed]
    public function carriers()
    {
        return BusinessPartner::where('status', 'ACTIVE')
            ->whereHas('roles', fn ($q) => $q->where('role_code', 'CARRIER'))
            ->orderBy('legal_name')
            ->get();
    }

    #[Computed]
    public function serviceTypes()
    {
        return ServiceType::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function locations()
    {
        return Location::where('is_active', true)->orderBy('name')->limit(60)->get();
    }

    #[Computed]
    public function packageTypes()
    {
        return PackageType::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function containerTypes()
    {
        return ContainerType::where('is_active', true)->orderBy('code')->get();
    }

    public function render()
    {
        return view('livewire.operations.shipment-manager');
    }
}
