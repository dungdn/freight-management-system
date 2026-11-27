<?php

namespace App\Livewire\Sales;

use App\Models\BusinessPartner;
use App\Models\ChargeCode;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Location;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\ServiceType;
use App\Services\Sales\SalesToShipmentService;
use App\Support\Tenancy\TenantContext;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class QuotationManager extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $selectedStatus = 'ALL';
    public string $selectedDirection = 'ALL';
    public int $perPage = 10;

    // Modals & Drawers
    public bool $showCreateModal = false;
    public bool $showDetailDrawer = false;
    public ?int $detailQuotationId = null;

    // Notification toast
    public ?string $feedbackMessage = null;
    public string $feedbackType = 'success';

    // Create form fields
    public $business_partner_id = null;
    public string $direction = 'OUTBOUND';
    public string $transport_mode = 'OCEAN';
    public $service_type_id = null;
    public $currency_id = null;
    public $origin_location_id = null;
    public $destination_location_id = null;
    public string $valid_until = '';
    public string $remarks = '';

    // Dynamic lines
    public array $lines = [];

    public function mount(): void
    {
        if (!TenantContext::getCompanyId()) {
            $defaultCompanyId = Company::where('is_active', true)->value('id');
            if ($defaultCompanyId) {
                TenantContext::setCompanyId($defaultCompanyId);
            }
        }

        $defaultCurr = Currency::where('code', 'USD')->first() ?? Currency::first();
        $this->currency_id = $defaultCurr?->id;
        $this->valid_until = now()->addDays(30)->format('Y-m-d');
        $this->resetLines();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedDirection(): void
    {
        $this->resetPage();
    }

    public function resetLines(): void
    {
        $this->lines = [
            [
                'charge_code_id' => null,
                'description' => 'Ocean Freight 40HC',
                'unit' => 'CONT',
                'quantity' => 1,
                'unit_price' => 1200,
                'cost_unit_price' => 950,
            ],
        ];
    }

    public function addLine(): void
    {
        $this->lines[] = [
            'charge_code_id' => null,
            'description' => '',
            'unit' => 'CONT',
            'quantity' => 1,
            'unit_price' => 0,
            'cost_unit_price' => 0,
        ];
    }

    public function removeLine(int $index): void
    {
        if (count($this->lines) > 1) {
            unset($this->lines[$index]);
            $this->lines = array_values($this->lines);
        }
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->business_partner_id = null;
        $this->direction = 'OUTBOUND';
        $this->transport_mode = 'OCEAN';
        $this->service_type_id = null;
        $this->origin_location_id = null;
        $this->destination_location_id = null;
        $this->remarks = '';
        $this->valid_until = now()->addDays(30)->format('Y-m-d');
        $this->resetLines();

        $this->showCreateModal = true;
    }

    public function closeCreate(): void
    {
        $this->showCreateModal = false;
    }

    public function openDetail(int $id): void
    {
        $this->detailQuotationId = $id;
        $this->showDetailDrawer = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailDrawer = false;
        $this->detailQuotationId = null;
    }

    public function createQuotation(): void
    {
        $this->validate([
            'business_partner_id' => 'required',
            'currency_id' => 'required',
            'valid_until' => 'required|date',
            'lines' => 'required|array|min:1',
            'lines.*.description' => 'required|string|max:190',
            'lines.*.quantity' => 'required|numeric|min:0.0001',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ]);

        $partnerId = !empty($this->business_partner_id) ? (int) $this->business_partner_id : null;
        $currId = !empty($this->currency_id) ? (int) $this->currency_id : null;
        $serviceTypeId = !empty($this->service_type_id) ? (int) $this->service_type_id : null;
        $originLocId = !empty($this->origin_location_id) ? (int) $this->origin_location_id : null;
        $destLocId = !empty($this->destination_location_id) ? (int) $this->destination_location_id : null;

        $service = app(SalesToShipmentService::class);

        $quotation = $service->createQuotation([
            'business_partner_id' => $partnerId,
            'direction' => $this->direction,
            'transport_mode' => $this->transport_mode,
            'service_type_id' => $serviceTypeId,
            'origin_location_id' => $originLocId,
            'destination_location_id' => $destLocId,
            'currency_id' => $currId,
            'valid_until' => $this->valid_until,
            'remarks' => $this->remarks,
        ], $this->lines);

        $this->showCreateModal = false;
        $this->feedbackType = 'success';
        $this->feedbackMessage = "Quotation #{$quotation->quotation_number} created successfully!";

        // Open newly created quotation detail
        $this->openDetail($quotation->id);
    }

    public function acceptQuote(int $id): void
    {
        try {
            $service = app(SalesToShipmentService::class);
            $salesOrder = $service->acceptQuotation($id);

            $this->feedbackType = 'success';
            $this->feedbackMessage = "Quotation accepted! Sales Order #{$salesOrder->sales_order_number} has been generated.";
        } catch (\Throwable $e) {
            $this->feedbackType = 'error';
            $this->feedbackMessage = $e->getMessage();
        }
    }

    public function convertOrderToShipment(int $salesOrderId): void
    {
        try {
            $service = app(SalesToShipmentService::class);
            $shipment = $service->convertToShipment($salesOrderId);

            $this->feedbackType = 'success';
            $this->feedbackMessage = "Operational Shipment #{$shipment->shipment_number} created successfully!";
        } catch (\Throwable $e) {
            $this->feedbackType = 'error';
            $this->feedbackMessage = $e->getMessage();
        }
    }

    public function quickConvertQuotationToShipment(int $quotationId): void
    {
        try {
            $service = app(SalesToShipmentService::class);
            $salesOrder = $service->acceptQuotation($quotationId);
            $shipment = $service->convertToShipment($salesOrder);

            $this->feedbackType = 'success';
            $this->feedbackMessage = "Full workflow complete: Sales Order #{$salesOrder->sales_order_number} & Shipment #{$shipment->shipment_number} created!";
        } catch (\Throwable $e) {
            $this->feedbackType = 'error';
            $this->feedbackMessage = $e->getMessage();
        }
    }

    #[Computed]
    public function quotations()
    {
        $query = Quotation::query()
            ->with(['customer', 'revisions.lines', 'salesOrder.shipment'])
            ->latest('id');

        if (!empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('quotation_number', 'like', $term)
                    ->orWhereHas('customer', function ($sub) use ($term) {
                        $sub->where('legal_name', 'like', $term)
                            ->orWhere('partner_code', 'like', $term);
                    });
            });
        }

        if ($this->selectedStatus !== 'ALL') {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->selectedDirection !== 'ALL') {
            $query->where('direction', $this->selectedDirection);
        }

        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function detailQuotation(): ?Quotation
    {
        if (!$this->detailQuotationId) {
            return null;
        }

        return Quotation::with([
            'customer',
            'revisions.lines.chargeCode',
            'revisions.currency',
            'serviceType',
            'originLocation',
            'destinationLocation',
            'salesOrder.shipment',
        ])->find($this->detailQuotationId);
    }

    #[Computed]
    public function partners()
    {
        return BusinessPartner::where('status', 'ACTIVE')->orderBy('legal_name')->get();
    }

    #[Computed]
    public function currencies()
    {
        return Currency::where('is_active', true)->orderBy('code')->get();
    }

    #[Computed]
    public function serviceTypes()
    {
        return ServiceType::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function chargeCodes()
    {
        return ChargeCode::where('is_active', true)->orderBy('code')->get();
    }

    #[Computed]
    public function locations()
    {
        return Location::where('is_active', true)->orderBy('name')->limit(50)->get();
    }

    public function render()
    {
        return view('livewire.sales.quotation-manager');
    }
}
