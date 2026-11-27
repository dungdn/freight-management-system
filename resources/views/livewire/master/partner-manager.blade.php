<div class="space-y-6">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/20 dark:text-emerald-300">
            <svg class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 4 KPI Overview Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 md:gap-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Partners</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ $totalCount }}</h4>
                <span class="text-xs text-brand-600 dark:text-brand-400 font-medium">Global Directory</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Customers & Shippers</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ $customerCount }}</h4>
                <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">BCO & Trading</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Carriers & Lines</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ $carrierCount }}</h4>
                <span class="text-xs text-purple-600 dark:text-purple-400 font-medium">Ocean & Air</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Overseas Agents</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ $agentCount }}</h4>
                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Global Network</span>
            </div>
        </div>
    </div>

    <!-- Main Data Table Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/3">
        <!-- Toolbar: Search, Filters, New Partner Button -->
        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                <!-- Search Input -->
                <div class="relative w-full sm:w-72">
                    <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 text-gray-400">
                        <x-svg.search class="size-4" />
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search code, name, tax code..."
                        class="w-full rounded-lg border border-gray-200 bg-transparent py-2 pe-3 ps-9 text-xs text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                </div>

                <!-- Role Filter -->
                <div class="w-full sm:w-48">
                    <select wire:model.live="selectedRole"
                        class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-700 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                        <option value="ALL">All Roles</option>
                        @foreach ($availableRoles as $code => $label)
                            <option value="{{ $code }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-36">
                    <select wire:model.live="selectedStatus"
                        class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-700 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                        <option value="ALL">All Status</option>
                        <option value="ACTIVE">Active</option>
                        <option value="HOLD">Hold</option>
                        <option value="INACTIVE">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Action Button -->
            <button wire:click="openCreate"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-brand-600 transition">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Partner</span>
            </button>
        </div>

        <!-- Table -->
        <div class="max-w-full overflow-x-auto">
            <table class="min-w-full text-start text-xs">
                <thead class="border-y border-gray-100 bg-gray-50/60 dark:border-gray-800 dark:bg-white/[0.02]">
                    <tr>
                        <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Partner Code & Name</th>
                        <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Kind / Tax Code</th>
                        <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Assigned Roles</th>
                        <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Primary Contact</th>
                        <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Payment Term & Credit</th>
                        <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="py-3 px-3 text-end font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($partners as $partner)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                            <!-- Code & Name -->
                            <td class="py-3 px-3">
                                <button wire:click="openDetail({{ $partner->id }})" class="font-semibold text-brand-600 hover:underline dark:text-brand-400 text-start">
                                    {{ $partner->partner_code }}
                                </button>
                                <p class="mt-0.5 font-medium text-gray-800 dark:text-white/90">
                                    {{ $partner->legal_name }}
                                </p>
                                @if($partner->display_name && $partner->display_name !== $partner->legal_name)
                                    <p class="text-[11px] text-gray-400">Trade: {{ $partner->display_name }}</p>
                                @endif
                            </td>

                            <!-- Kind & Tax Code -->
                            <td class="py-3 px-3">
                                <span class="text-gray-700 dark:text-gray-300 font-medium">
                                    {{ $partner->tax_code ?: 'N/A' }}
                                </span>
                                <p class="mt-0.5 text-[11px] text-gray-400">
                                    {{ $partner->partner_kind === 'ORGANIZATION' ? 'Company' : 'Individual' }}
                                </p>
                            </td>

                            <!-- Roles -->
                            <td class="py-3 px-3">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($partner->roles as $role)
                                        <span class="inline-block rounded px-1.5 py-0.5 text-[10px] font-semibold tracking-wide
                                            @if(in_array($role->role_code, ['CUSTOMER', 'SHIPPER'])) bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400
                                            @elseif(in_array($role->role_code, ['CARRIER'])) bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400
                                            @elseif(in_array($role->role_code, ['AGENT'])) bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400
                                            @elseif(in_array($role->role_code, ['TRUCKING_VENDOR'])) bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400
                                            @else bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 @endif">
                                            {{ $role->role_code }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 text-[11px]">No roles</span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- Primary Contact -->
                            <td class="py-3 px-3">
                                @if ($partner->primaryContact)
                                    <p class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ $partner->primaryContact->full_name }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                        {{ $partner->primaryContact->email ?: $partner->primaryContact->phone }}
                                    </p>
                                @else
                                    <span class="text-gray-400 text-[11px]">No contact</span>
                                @endif
                            </td>

                            <!-- Payment Term & Credit -->
                            <td class="py-3 px-3">
                                <p class="font-medium text-gray-700 dark:text-gray-300">
                                    {{ $partner->paymentTerm?->name ?? 'Standard' }}
                                </p>
                                @if ($partner->activeCreditProfile && $partner->activeCreditProfile->credit_limit > 0)
                                    <p class="mt-0.5 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($partner->activeCreditProfile->credit_limit) }} {{ $partner->activeCreditProfile->currency?->code }} ({{ $partner->activeCreditProfile->credit_days }}d)
                                    </p>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 px-3">
                                <button wire:click="toggleStatus({{ $partner->id }})" title="Click to toggle status"
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-medium transition cursor-pointer
                                    @if($partner->status === 'ACTIVE') bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400
                                    @elseif($partner->status === 'HOLD') bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400
                                    @else bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 @endif">
                                    <span class="size-1.5 rounded-full 
                                        @if($partner->status === 'ACTIVE') bg-emerald-500
                                        @elseif($partner->status === 'HOLD') bg-amber-500
                                        @else bg-gray-400 @endif"></span>
                                    <span>{{ $partner->status }}</span>
                                </button>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-3 px-3 text-end">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="openDetail({{ $partner->id }})" title="View Details"
                                        class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white transition">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="openEdit({{ $partner->id }})" title="Edit Partner"
                                        class="rounded-lg p-1.5 text-brand-600 hover:bg-brand-50 hover:text-brand-700 dark:text-brand-400 dark:hover:bg-brand-500/10 transition">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:confirm="Are you sure you want to remove this partner and all related data?"
                                        wire:click="deletePartner({{ $partner->id }})" title="Delete Partner"
                                        class="rounded-lg p-1.5 text-error-500 hover:bg-error-50 hover:text-error-600 dark:hover:bg-error-500/10 transition">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="size-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="font-medium">No business partners found matching your search.</p>
                                    <button wire:click="openCreate" class="mt-3 text-xs font-semibold text-brand-600 hover:underline">
                                        + Add the first partner
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
            {{ $partners->links() }}
        </div>
    </div>

    <!-- Create / Edit Partner Modal -->
    @if ($showFormModal)
        <div class="fixed inset-0 z-999 flex items-center justify-center overflow-y-auto bg-gray-900/60 p-4 backdrop-blur-xs"
             @keydown.escape.window="$wire.closeModal()"
             @click.self="$wire.closeModal()">
            <div class="relative w-full max-w-3xl rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900 dark:border dark:border-gray-800 max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                            {{ $isEditing ? 'Edit Business Partner' : 'Create New Business Partner' }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $isEditing ? 'Update partner profile, commercial terms, and contact information' : 'Add a new client, carrier, agent, or vendor to Cargo FMS' }}
                        </p>
                    </div>
                    <button wire:click="closeModal" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800">
                        <x-svg.x class="size-5" />
                    </button>
                </div>

                <!-- Form Content -->
                <form wire:submit="save" class="mt-5 space-y-5">
                    <!-- General Information -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                Partner Code <span class="text-error-500">*</span>
                            </label>
                            <input type="text" wire:model="partner_code" placeholder="e.g. CUST_VINFAST, LINE_ONE"
                                class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs uppercase text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                            @error('partner_code') <span class="text-[11px] text-error-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                Partner Kind <span class="text-error-500">*</span>
                            </label>
                            <select wire:model="partner_kind"
                                class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="ORGANIZATION">Organization / Enterprise</option>
                                <option value="INDIVIDUAL">Individual / Shipper Person</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                Registered Legal Name <span class="text-error-500">*</span>
                            </label>
                            <input type="text" wire:model="legal_name" placeholder="Full registered company legal name"
                                class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                            @error('legal_name') <span class="text-[11px] text-error-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                Trade / Display Name
                            </label>
                            <input type="text" wire:model="display_name" placeholder="Short brand name"
                                class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                Tax Code / Registration No
                            </label>
                            <input type="text" wire:model="tax_code" placeholder="Tax identification code"
                                class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                        </div>
                    </div>

                    <!-- Roles Selection -->
                    <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
                        <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                            Partner Roles <span class="text-error-500">* (Select at least one)</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($availableRoles as $code => $label)
                                <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                    <input type="checkbox" wire:model="roles" value="{{ $code }}"
                                        class="size-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800" />
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('roles') <span class="text-[11px] text-error-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Commercial & Accounting Terms -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">Default Currency</label>
                            <select wire:model="default_currency_id"
                                class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Select Currency</option>
                                @foreach ($currencies as $curr)
                                    <option value="{{ $curr->id }}">{{ $curr->code }} - {{ $curr->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">Payment Term</label>
                            <select wire:model="payment_term_id"
                                class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Select Payment Term</option>
                                @foreach ($paymentTerms as $term)
                                    <option value="{{ $term->id }}">{{ $term->code }} ({{ $term->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">Status</label>
                            <select wire:model="status"
                                class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="ACTIVE">ACTIVE</option>
                                <option value="HOLD">HOLD</option>
                                <option value="INACTIVE">INACTIVE</option>
                            </select>
                        </div>
                    </div>

                    <!-- Primary Contact Section -->
                    <div class="border-t border-gray-100 pt-4 dark:border-gray-800">
                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Primary Contact Person
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Full Name</label>
                                <input type="text" wire:model="contact_name" placeholder="Contact person name"
                                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Job Title / Department</label>
                                <input type="text" wire:model="contact_title" placeholder="e.g. Logistics Manager"
                                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Email Address</label>
                                <input type="email" wire:model="contact_email" placeholder="contact@company.com"
                                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Phone / Mobile</label>
                                <input type="text" wire:model="contact_phone" placeholder="+84 90 123 4567"
                                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                            </div>
                        </div>
                    </div>

                    <!-- Address & Credit Section -->
                    <div class="border-t border-gray-100 pt-4 dark:border-gray-800">
                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Primary Address & Credit Profile
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Street Address</label>
                                <input type="text" wire:model="address_line1" placeholder="Address line 1"
                                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">City / Province</label>
                                <input type="text" wire:model="city" placeholder="e.g. Ho Chi Minh City"
                                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Country</label>
                                <select wire:model="country_id"
                                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->iso2 }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Credit Limit</label>
                                <input type="number" step="0.01" wire:model="credit_limit" placeholder="e.g. 50000"
                                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Credit Terms (Days)</label>
                                <input type="number" wire:model="credit_days" placeholder="30"
                                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <button type="button" wire:click="closeModal"
                            class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-brand-600 transition">
                            <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Save Changes' : 'Create Partner' }}</span>
                            <span wire:loading wire:target="save">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Detail Slide-Over Drawer -->
    @if ($showDetailDrawer && $this->detailPartner)
        <div class="fixed inset-0 z-999 flex justify-end bg-gray-900/40 backdrop-blur-xs"
             @keydown.escape.window="$wire.closeDetailDrawer()"
             @click.self="$wire.closeDetailDrawer()">
            <div class="h-full w-full max-w-lg bg-white p-6 shadow-2xl dark:bg-gray-900 dark:border-s dark:border-gray-800 overflow-y-auto">
                <!-- Drawer Header -->
                <div class="flex items-start justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                    <div>
                        <span class="inline-block rounded-full bg-brand-50 px-2.5 py-0.5 text-[11px] font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            {{ $this->detailPartner->partner_code }}
                        </span>
                        <h3 class="mt-1 text-base font-bold text-gray-800 dark:text-white/90">
                            {{ $this->detailPartner->legal_name }}
                        </h3>
                        @if($this->detailPartner->display_name)
                            <p class="text-xs text-gray-500 dark:text-gray-400">Trade: {{ $this->detailPartner->display_name }}</p>
                        @endif
                    </div>
                    <button wire:click="closeDetailDrawer" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800">
                        <x-svg.x class="size-5" />
                    </button>
                </div>

                <!-- Drawer Content -->
                <div class="mt-5 space-y-6 text-xs">
                    <!-- Status & Kind -->
                    <div class="grid grid-cols-2 gap-3 rounded-xl border border-gray-100 bg-gray-50/70 p-3.5 dark:border-gray-800 dark:bg-white/[0.02]">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Status</span>
                            <p class="mt-0.5 font-bold text-emerald-600 dark:text-emerald-400">{{ $this->detailPartner->status }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Partner Kind</span>
                            <p class="mt-0.5 font-medium text-gray-800 dark:text-white">{{ $this->detailPartner->partner_kind }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Tax Code</span>
                            <p class="mt-0.5 font-mono text-gray-800 dark:text-white">{{ $this->detailPartner->tax_code ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Default Currency</span>
                            <p class="mt-0.5 font-semibold text-gray-800 dark:text-white">{{ $this->detailPartner->defaultCurrency?->code ?: 'USD' }}</p>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div>
                        <h4 class="mb-2 font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">Assigned Roles</h4>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($this->detailPartner->roles as $role)
                                <span class="rounded bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                    {{ $role->role_code }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Primary Contact -->
                    <div>
                        <h4 class="mb-2 font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">Contacts</h4>
                        @forelse ($this->detailPartner->contacts as $contact)
                            <div class="rounded-xl border border-gray-100 p-3 dark:border-gray-800 mb-2">
                                <div class="flex justify-between items-center">
                                    <p class="font-bold text-gray-800 dark:text-white">{{ $contact->full_name }}</p>
                                    @if($contact->is_primary)
                                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-medium text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">Primary</span>
                                    @endif
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">{{ $contact->job_title }}</p>
                                <p class="mt-1 text-gray-700 dark:text-gray-300">📧 {{ $contact->email ?: 'N/A' }} · 📞 {{ $contact->phone ?: 'N/A' }}</p>
                            </div>
                        @empty
                            <p class="text-gray-400">No contacts recorded.</p>
                        @endforelse
                    </div>

                    <!-- Addresses -->
                    <div>
                        <h4 class="mb-2 font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">Addresses</h4>
                        @forelse ($this->detailPartner->addresses as $addr)
                            <div class="rounded-xl border border-gray-100 p-3 dark:border-gray-800 mb-2">
                                <span class="text-[11px] font-semibold text-brand-600 dark:text-brand-400">{{ $addr->address_type }}</span>
                                <p class="mt-1 text-gray-800 dark:text-white">{{ $addr->address_line1 }}</p>
                                <p class="text-gray-500 dark:text-gray-400">{{ $addr->city }} · {{ $addr->country?->name }}</p>
                            </div>
                        @empty
                            <p class="text-gray-400">No address recorded.</p>
                        @endforelse
                    </div>

                    <!-- Credit Profile -->
                    <div>
                        <h4 class="mb-2 font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">Credit & Risk Profile</h4>
                        @if ($this->detailPartner->activeCreditProfile)
                            <div class="rounded-xl border border-emerald-200/60 bg-emerald-50/40 p-3.5 dark:border-emerald-900/30 dark:bg-emerald-950/10">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Approved Credit Limit</span>
                                    <span class="font-bold text-emerald-700 dark:text-emerald-400">
                                        {{ number_format($this->detailPartner->activeCreditProfile->credit_limit) }} {{ $this->detailPartner->activeCreditProfile->currency?->code }}
                                    </span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-gray-600 dark:text-gray-400">Payment Days</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $this->detailPartner->activeCreditProfile->credit_days }} days</span>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-400">No active credit profile (Standard COD / Cash terms apply).</p>
                        @endif
                    </div>
                </div>

                <!-- Drawer Footer Actions -->
                <div class="mt-6 flex items-center gap-2 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <button wire:click="openEdit({{ $this->detailPartner->id }})"
                        class="flex-1 rounded-lg bg-brand-500 py-2 text-center text-xs font-semibold text-white hover:bg-brand-600 transition">
                        Edit Partner
                    </button>
                    <button wire:click="closeDetailDrawer"
                        class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
