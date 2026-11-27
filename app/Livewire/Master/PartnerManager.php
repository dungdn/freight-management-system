<?php

namespace App\Livewire\Master;

use App\Models\BusinessPartner;
use App\Models\Company;
use App\Models\Country;
use App\Models\Currency;
use App\Models\PartnerAddress;
use App\Models\PartnerContact;
use App\Models\PartnerCreditProfile;
use App\Models\PartnerRole;
use App\Models\PaymentTerm;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class PartnerManager extends Component
{
    use WithPagination;

    // Filters & Pagination
    public string $search = '';
    public string $selectedRole = 'ALL';
    public string $selectedStatus = 'ALL';
    public int $perPage = 10;

    // Modal & Drawer visibility
    public bool $showFormModal = false;
    public bool $showDetailDrawer = false;
    public bool $isEditing = false;
    public ?int $selectedPartnerId = null;
    public ?int $detailPartnerId = null;

    // Form inputs
    public string $partner_code = '';
    public string $legal_name = '';
    public string $display_name = '';
    public string $partner_kind = 'ORGANIZATION';
    public string $tax_code = '';
    public string $registration_no = '';
    public string $website = '';
    public $default_currency_id = null;
    public $payment_term_id = null;
    public string $status = 'ACTIVE';
    public string $notes = '';

    // Selected Roles (array of strings e.g. ['CUSTOMER', 'SHIPPER'])
    public array $roles = [];

    // Primary Contact inputs
    public string $contact_name = '';
    public string $contact_title = '';
    public string $contact_email = '';
    public string $contact_phone = '';

    // Primary Address inputs
    public string $address_line1 = '';
    public string $city = '';
    public $country_id = null;

    // Credit Profile inputs
    public string $credit_limit = '';
    public $credit_currency_id = null;
    public $credit_days = 0;

    // Available role constants
    public const AVAILABLE_ROLES = [
        'CUSTOMER' => 'Customer',
        'SHIPPER' => 'Shipper (Consignor)',
        'CONSIGNEE' => 'Consignee',
        'CARRIER' => 'Carrier (Shipping Line / Airline)',
        'AGENT' => 'Overseas Agent',
        'COLOADER' => 'Co-loader / Consolidator',
        'TRUCKING_VENDOR' => 'Trucking Vendor',
        'CUSTOMS_BROKER' => 'Customs Broker',
        'VENDOR' => 'General Vendor',
    ];

    public function mount(): void
    {
        if (!TenantContext::getCompanyId()) {
            $defaultCompanyId = Company::where('is_active', true)->value('id');
            if ($defaultCompanyId) {
                TenantContext::setCompanyId($defaultCompanyId);
            }
        }
    }

    protected function rules(): array
    {
        $companyId = TenantContext::getCompanyId();

        return [
            'partner_code' => [
                'required',
                'string',
                'max:30',
                $this->isEditing
                    ? "unique:business_partners,partner_code,{$this->selectedPartnerId},id,company_id,{$companyId}"
                    : "unique:business_partners,partner_code,NULL,id,company_id,{$companyId}",
            ],
            'legal_name' => 'required|string|max:200',
            'display_name' => 'nullable|string|max:200',
            'partner_kind' => 'required|in:ORGANIZATION,INDIVIDUAL',
            'tax_code' => 'nullable|string|max:40',
            'registration_no' => 'nullable|string|max:60',
            'website' => 'nullable|string|max:255',
            'default_currency_id' => 'nullable',
            'payment_term_id' => 'nullable',
            'status' => 'required|in:ACTIVE,INACTIVE,HOLD',
            'notes' => 'nullable|string|max:1000',
            'roles' => 'required|array|min:1',
            'contact_name' => 'nullable|string|max:160',
            'contact_email' => 'nullable|email|max:190',
            'contact_phone' => 'nullable|string|max:40',
            'address_line1' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:100',
            'country_id' => 'nullable',
            'credit_limit' => 'nullable|numeric|min:0',
            'credit_days' => 'nullable|integer|min:0',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedRole(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function detailPartner(): ?BusinessPartner
    {
        if (!$this->detailPartnerId) {
            return null;
        }

        return BusinessPartner::with([
            'defaultCurrency',
            'paymentTerm',
            'roles',
            'contacts',
            'addresses.country',
            'activeCreditProfile.currency',
            'salesAssignments.salesUser',
        ])->find($this->detailPartnerId);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->selectedPartnerId = null;

        // Sensible defaults
        $defaultCurrency = Currency::where('code', 'USD')->first() ?? Currency::first();
        $this->default_currency_id = $defaultCurrency?->id;
        $this->credit_currency_id = $defaultCurrency?->id;
        $this->country_id = Country::where('iso2', 'VN')->first()?->id ?? Country::first()?->id;
        $this->roles = ['CUSTOMER', 'SHIPPER'];

        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->selectedPartnerId = $id;

        $partner = BusinessPartner::with(['roles', 'primaryContact', 'primaryAddress', 'activeCreditProfile'])->findOrFail($id);

        $this->partner_code = $partner->partner_code;
        $this->legal_name = $partner->legal_name;
        $this->display_name = $partner->display_name ?? '';
        $this->partner_kind = $partner->partner_kind;
        $this->tax_code = $partner->tax_code ?? '';
        $this->registration_no = $partner->registration_no ?? '';
        $this->website = $partner->website ?? '';
        $this->default_currency_id = $partner->default_currency_id;
        $this->payment_term_id = $partner->payment_term_id;
        $this->status = $partner->status;
        $this->notes = $partner->notes ?? '';
        $this->roles = $partner->roles->pluck('role_code')->all();

        if ($partner->primaryContact) {
            $this->contact_name = $partner->primaryContact->full_name;
            $this->contact_title = $partner->primaryContact->job_title ?? '';
            $this->contact_email = $partner->primaryContact->email ?? '';
            $this->contact_phone = $partner->primaryContact->phone ?? '';
        }

        if ($partner->primaryAddress) {
            $this->address_line1 = $partner->primaryAddress->address_line1;
            $this->city = $partner->primaryAddress->city ?? '';
            $this->country_id = $partner->primaryAddress->country_id;
        }

        if ($partner->activeCreditProfile) {
            $this->credit_limit = (string) $partner->activeCreditProfile->credit_limit;
            $this->credit_currency_id = $partner->activeCreditProfile->currency_id;
            $this->credit_days = (int) $partner->activeCreditProfile->credit_days;
        }

        $this->showFormModal = true;
    }

    public function openDetail(int $id): void
    {
        $this->detailPartnerId = $id;
        $this->selectedPartnerId = $id;
        $this->showDetailDrawer = true;
    }

    public function closeDetailDrawer(): void
    {
        $this->showDetailDrawer = false;
        $this->detailPartnerId = null;
    }

    public function closeModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate();
        $companyId = TenantContext::getCompanyId();

        $defaultCurrId = !empty($this->default_currency_id) ? (int) $this->default_currency_id : null;
        $paymentTermId = !empty($this->payment_term_id) ? (int) $this->payment_term_id : null;
        $countryId = !empty($this->country_id) ? (int) $this->country_id : null;
        $creditCurrId = !empty($this->credit_currency_id) ? (int) $this->credit_currency_id : $defaultCurrId;
        $creditDays = !empty($this->credit_days) ? (int) $this->credit_days : 0;

        DB::transaction(function () use ($companyId, $defaultCurrId, $paymentTermId, $countryId, $creditCurrId, $creditDays) {
            $partnerData = [
                'company_id' => $companyId,
                'partner_code' => strtoupper(trim($this->partner_code)),
                'legal_name' => trim($this->legal_name),
                'display_name' => trim($this->display_name) ?: null,
                'partner_kind' => $this->partner_kind,
                'tax_code' => trim($this->tax_code) ?: null,
                'registration_no' => trim($this->registration_no) ?: null,
                'website' => trim($this->website) ?: null,
                'default_currency_id' => $defaultCurrId,
                'payment_term_id' => $paymentTermId,
                'status' => $this->status,
                'notes' => trim($this->notes) ?: null,
            ];

            if ($this->isEditing && $this->selectedPartnerId) {
                $partner = BusinessPartner::findOrFail($this->selectedPartnerId);
                $partner->update($partnerData);
            } else {
                $partner = BusinessPartner::create($partnerData);
            }

            // Sync Roles
            PartnerRole::where('business_partner_id', $partner->id)->delete();
            foreach ($this->roles as $roleCode) {
                PartnerRole::create([
                    'company_id' => $companyId,
                    'business_partner_id' => $partner->id,
                    'role_code' => strtoupper($roleCode),
                    'is_active' => true,
                    'effective_from' => now()->toDateString(),
                ]);
            }

            // Save Primary Contact if provided
            if (!empty($this->contact_name)) {
                PartnerContact::updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'business_partner_id' => $partner->id,
                        'is_primary' => true,
                    ],
                    [
                        'full_name' => trim($this->contact_name),
                        'job_title' => trim($this->contact_title) ?: null,
                        'email' => trim($this->contact_email) ?: null,
                        'phone' => trim($this->contact_phone) ?: null,
                        'is_active' => true,
                    ]
                );
            }

            // Save Primary Address if provided
            if (!empty($this->address_line1) && $countryId) {
                PartnerAddress::updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'business_partner_id' => $partner->id,
                        'is_default' => true,
                    ],
                    [
                        'address_type' => 'OFFICE',
                        'address_line1' => trim($this->address_line1),
                        'city' => trim($this->city) ?: null,
                        'country_id' => $countryId,
                        'is_active' => true,
                    ]
                );
            }

            // Save Credit Profile if provided
            if ($this->credit_limit !== '' && $this->credit_limit !== null) {
                PartnerCreditProfile::updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'business_partner_id' => $partner->id,
                        'is_active' => true,
                    ],
                    [
                        'credit_limit' => (float) $this->credit_limit,
                        'currency_id' => $creditCurrId ?? 1,
                        'credit_days' => $creditDays,
                        'effective_from' => now()->toDateString(),
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]
                );
            }
        });

        session()->flash('success', $this->isEditing ? 'Business partner updated successfully.' : 'Business partner created successfully.');

        $this->closeModal();
    }

    public function toggleStatus(int $id): void
    {
        $partner = BusinessPartner::findOrFail($id);
        $partner->status = match ($partner->status) {
            'ACTIVE' => 'HOLD',
            'HOLD' => 'INACTIVE',
            'INACTIVE' => 'ACTIVE',
            default => 'ACTIVE',
        };
        $partner->save();

        session()->flash('success', "Partner status updated to {$partner->status}.");
    }

    public function deletePartner(int $id): void
    {
        $partner = BusinessPartner::findOrFail($id);
        $code = $partner->partner_code;

        DB::transaction(function () use ($partner) {
            $partner->roles()->delete();
            $partner->contacts()->delete();
            $partner->addresses()->delete();
            $partner->creditProfiles()->delete();
            $partner->salesAssignments()->delete();
            $partner->delete();
        });

        if ($this->showDetailDrawer && $this->selectedPartnerId === $id) {
            $this->closeDetailDrawer();
        }

        session()->flash('success', "Business partner {$code} removed successfully.");
    }

    protected function resetForm(): void
    {
        $this->reset([
            'partner_code',
            'legal_name',
            'display_name',
            'partner_kind',
            'tax_code',
            'registration_no',
            'website',
            'default_currency_id',
            'payment_term_id',
            'status',
            'notes',
            'roles',
            'contact_name',
            'contact_title',
            'contact_email',
            'contact_phone',
            'address_line1',
            'city',
            'country_id',
            'credit_limit',
            'credit_currency_id',
            'credit_days',
        ]);
        $this->resetErrorBag();
    }

    public function render()
    {
        $companyId = TenantContext::getCompanyId();

        // Summary counts
        $totalCount = BusinessPartner::count();
        $customerCount = BusinessPartner::whereHas('roles', fn ($q) => $q->whereIn('role_code', ['CUSTOMER', 'SHIPPER', 'CONSIGNEE']))->count();
        $carrierCount = BusinessPartner::whereHas('roles', fn ($q) => $q->where('role_code', 'CARRIER'))->count();
        $agentCount = BusinessPartner::whereHas('roles', fn ($q) => $q->where('role_code', 'AGENT'))->count();

        // Query with search and filters
        $partners = BusinessPartner::query()
            ->with(['roles', 'primaryContact', 'primaryAddress.country', 'activeCreditProfile.currency', 'paymentTerm'])
            ->when($this->search !== '', function ($q) {
                $term = '%' . trim($this->search) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('partner_code', 'like', $term)
                        ->orWhere('legal_name', 'like', $term)
                        ->orWhere('display_name', 'like', $term)
                        ->orWhere('tax_code', 'like', $term)
                        ->orWhereHas('contacts', fn ($c) => $c->where('email', 'like', $term)->orWhere('phone', 'like', $term));
                });
            })
            ->when($this->selectedRole !== 'ALL', function ($q) {
                $q->whereHas('roles', fn ($r) => $r->where('role_code', $this->selectedRole));
            })
            ->when($this->selectedStatus !== 'ALL', function ($q) {
                $q->where('status', $this->selectedStatus);
            })
            ->latest('id')
            ->paginate($this->perPage);

        $currencies = Currency::where('is_active', true)->get();
        $paymentTerms = PaymentTerm::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->orderBy('name')->get();

        return view('livewire.master.partner-manager', [
            'partners' => $partners,
            'totalCount' => $totalCount,
            'customerCount' => $customerCount,
            'carrierCount' => $carrierCount,
            'agentCount' => $agentCount,
            'currencies' => $currencies,
            'paymentTerms' => $paymentTerms,
            'countries' => $countries,
            'availableRoles' => self::AVAILABLE_ROLES,
        ]);
    }
}
