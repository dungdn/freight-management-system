<div class="space-y-6">
    <!-- Notification Toast / Alerts -->
    @if ($feedbackMessage)
        <div class="flex items-center justify-between gap-3 rounded-xl border p-4 text-sm {{ $feedbackType === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/20 dark:text-emerald-300' : 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/20 dark:text-rose-300' }}">
            <div class="flex items-center gap-3">
                @if ($feedbackType === 'success')
                    <svg class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="size-5 shrink-0 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @endif
                <span class="font-medium">{{ $feedbackMessage }}</span>
            </div>
            <button type="button" wire:click="$set('feedbackMessage', null)" class="text-xs opacity-70 hover:opacity-100">
                &times; Close
            </button>
        </div>
    @endif

    <!-- 4 Operations KPI Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 md:gap-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Operational Jobs</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                    {{ \App\Models\Shipment::count() }}
                </h4>
                <span class="text-xs text-brand-600 dark:text-brand-400 font-medium">Ocean, Air, Road</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Booking & Origin Prep</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                    {{ \App\Models\Shipment::whereIn('operational_status', ['BOOKING_PENDING', 'BOOKING_CONFIRMED', 'CARGO_RECEIVED'])->count() }}
                </h4>
                <span class="text-xs text-amber-600 font-medium">Pre-Carriage / Origin</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">In Transit (Main Leg)</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ \App\Models\Shipment::where('operational_status', 'IN_TRANSIT')->count() }}
                </h4>
                <span class="text-xs text-blue-600 font-medium">On Water / Flight</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Arrived & Delivered</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                    {{ \App\Models\Shipment::whereIn('operational_status', ['ARRIVED', 'CUSTOMS_CLEARED', 'DELIVERED'])->count() }}
                </h4>
                <span class="text-xs text-emerald-600 font-medium">Destination Delivery</span>
            </div>
        </div>
    </div>

    <!-- Main Shipments Workbench Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/3">
        <!-- Toolbar -->
        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-1 flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 text-gray-400">
                        <x-svg.search class="size-4" />
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search Job #, MBL, HBL, Vessel..."
                        class="w-full rounded-lg border border-gray-200 bg-transparent py-2 pe-3 ps-9 text-xs text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                </div>

                <!-- Transport Mode Filter -->
                <div class="w-full sm:w-36">
                    <select wire:model.live="selectedMode"
                        class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-700 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                        <option value="ALL">All Modes</option>
                        <option value="OCEAN">OCEAN (Sea)</option>
                        <option value="AIR">AIR (Air Freight)</option>
                        <option value="ROAD">ROAD (Trucking)</option>
                        <option value="RAIL">RAIL (Railway)</option>
                    </select>
                </div>

                <!-- Operational Status Filter -->
                <div class="w-full sm:w-44">
                    <select wire:model.live="selectedStatus"
                        class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-700 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                        <option value="ALL">All Milestones</option>
                        <option value="BOOKING_PENDING">Booking Pending</option>
                        <option value="BOOKING_CONFIRMED">Booking Confirmed</option>
                        <option value="CARGO_RECEIVED">Cargo Received</option>
                        <option value="IN_TRANSIT">In Transit (Departed)</option>
                        <option value="ARRIVED">Arrived at Port</option>
                        <option value="CUSTOMS_CLEARED">Customs Cleared</option>
                        <option value="DELIVERED">Delivered to Consignee</option>
                    </select>
                </div>

                <!-- Direction Filter -->
                <div class="w-full sm:w-36">
                    <select wire:model.live="selectedDirection"
                        class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-700 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                        <option value="ALL">All Directions</option>
                        <option value="OUTBOUND">Outbound Export</option>
                        <option value="INBOUND">Inbound Import</option>
                        <option value="DOMESTIC">Domestic</option>
                    </select>
                </div>
            </div>

            <!-- Create Shipment Button -->
            <button type="button" wire:click="openCreate"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-hidden">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Shipment Job</span>
            </button>
        </div>

        <!-- Shipments Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs text-gray-700 dark:text-gray-300">
                <thead class="border-b border-gray-100 bg-gray-50/75 text-gray-500 dark:border-gray-800 dark:bg-white/2 dark:text-gray-400">
                    <tr>
                        <th class="py-3 px-4 text-start font-semibold">Job Number</th>
                        <th class="py-3 px-4 text-start font-semibold">Customer / Trade</th>
                        <th class="py-3 px-4 text-start font-semibold">Route (Origin &rarr; Dest)</th>
                        <th class="py-3 px-4 text-start font-semibold">Carrier / Vessel / BL</th>
                        <th class="py-3 px-4 text-center font-semibold">Milestone Status</th>
                        <th class="py-3 px-4 text-center font-semibold">Quick Step</th>
                        <th class="py-3 px-4 text-end font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($this->shipments as $sh)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/2 transition-colors">
                            <!-- Job Number -->
                            <td class="py-3.5 px-4 font-mono font-medium text-brand-600 dark:text-brand-400">
                                <button type="button" wire:click="openDetail({{ $sh->id }})" class="hover:underline">
                                    {{ $sh->shipment_number }}
                                </button>
                                @if ($sh->sales_order_id)
                                    <div class="text-[10px] text-gray-400 font-normal">SO: {{ $sh->salesOrder?->sales_order_number }}</div>
                                @endif
                            </td>

                            <!-- Customer & Trade -->
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-gray-800 dark:text-white/90">
                                    {{ $sh->customer?->legal_name ?? '—' }}
                                </div>
                                <div class="text-[11px] text-gray-400 flex items-center gap-1">
                                    <span class="font-semibold">{{ $sh->transport_mode }}</span>
                                    <span>•</span>
                                    <span>{{ $sh->direction }}</span>
                                </div>
                            </td>

                            <!-- Route -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1 font-medium text-gray-800 dark:text-white/90">
                                    <span>{{ $sh->originLocation?->name ?? 'POL' }}</span>
                                    <span class="text-gray-400">&rarr;</span>
                                    <span>{{ $sh->destinationLocation?->name ?? 'POD' }}</span>
                                </div>
                                <div class="text-[11px] text-gray-400">
                                    ETD: {{ $sh->etd ? $sh->etd->format('d/m/Y') : 'TBD' }} • ETA: {{ $sh->eta ? $sh->eta->format('d/m/Y') : 'TBD' }}
                                </div>
                            </td>

                            <!-- Carrier / Vessel / BL -->
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-gray-800 dark:text-white/90">
                                    {{ $sh->carrier?->display_name ?? $sh->vessel_name ?? 'Pending Carrier' }}
                                    @if ($sh->voyage_no)
                                        <span class="text-gray-400 font-mono text-[11px]">({{ $sh->voyage_no }})</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-gray-400 font-mono">
                                    MBL: {{ $sh->master_bl_no ?? '—' }} | HBL: {{ $sh->house_bl_no ?? '—' }}
                                </div>
                            </td>

                            <!-- Milestone Status Badge -->
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $st = $sh->operational_status ?? 'BOOKING_PENDING';
                                    $badgeClass = match ($st) {
                                        'BOOKING_PENDING' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400',
                                        'BOOKING_CONFIRMED' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400',
                                        'CARGO_RECEIVED' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400',
                                        'IN_TRANSIT' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400',
                                        'ARRIVED' => 'bg-teal-50 text-teal-700 dark:bg-teal-950/40 dark:text-teal-400',
                                        'CUSTOMS_CLEARED' => 'bg-cyan-50 text-cyan-700 dark:bg-cyan-950/40 dark:text-cyan-400',
                                        'DELIVERED' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400',
                                        default => 'bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $badgeClass }}">
                                    <span class="size-1.5 rounded-full bg-current"></span>
                                    {{ str_replace('_', ' ', $st) }}
                                </span>
                            </td>

                            <!-- Quick Step Advance -->
                            <td class="py-3.5 px-4 text-center">
                                @if ($sh->operational_status === 'BOOKING_PENDING')
                                    <button type="button" wire:click="advanceMilestone({{ $sh->id }}, 'BOOKING_CONFIRMED')"
                                        class="rounded bg-blue-50 px-2 py-1 text-[11px] font-medium text-blue-600 hover:bg-blue-100 dark:bg-blue-950/40 dark:text-blue-400">
                                        Confirm Booking &rarr;
                                    </button>
                                @elseif ($sh->operational_status === 'BOOKING_CONFIRMED')
                                    <button type="button" wire:click="advanceMilestone({{ $sh->id }}, 'CARGO_RECEIVED')"
                                        class="rounded bg-purple-50 px-2 py-1 text-[11px] font-medium text-purple-600 hover:bg-purple-100 dark:bg-purple-950/40 dark:text-purple-400">
                                        Receive Cargo &rarr;
                                    </button>
                                @elseif ($sh->operational_status === 'CARGO_RECEIVED')
                                    <button type="button" wire:click="advanceMilestone({{ $sh->id }}, 'DEPARTED')"
                                        class="rounded bg-indigo-50 px-2 py-1 text-[11px] font-medium text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-400">
                                        Depart (ETD) &rarr;
                                    </button>
                                @elseif ($sh->operational_status === 'IN_TRANSIT')
                                    <button type="button" wire:click="advanceMilestone({{ $sh->id }}, 'ARRIVED')"
                                        class="rounded bg-teal-50 px-2 py-1 text-[11px] font-medium text-teal-600 hover:bg-teal-100 dark:bg-teal-950/40 dark:text-teal-400">
                                        Port Arrival &rarr;
                                    </button>
                                @elseif ($sh->operational_status === 'ARRIVED')
                                    <button type="button" wire:click="advanceMilestone({{ $sh->id }}, 'DELIVERED')"
                                        class="rounded bg-emerald-50 px-2 py-1 text-[11px] font-medium text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-400">
                                        Deliver Cargo &rarr;
                                    </button>
                                @elseif ($sh->operational_status === 'DELIVERED')
                                    <span class="text-emerald-600 text-[11px] font-medium">&check; Completed</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" wire:click="openDetail({{ $sh->id }})"
                                        class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5"
                                        title="View Shipment Workbench">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <button type="button" wire:click="openEventModal({{ $sh->id }})"
                                        class="rounded-lg p-1.5 text-blue-500 hover:bg-blue-50 hover:text-blue-700 dark:text-blue-400 dark:hover:bg-blue-950/40"
                                        title="Log Milestone Event">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No shipments found. Convert an accepted Quotation or click <strong class="text-brand-500 font-semibold cursor-pointer" wire:click="openCreate">New Shipment Job</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->shipments->links() }}
        </div>
    </div>

    <!-- Create Shipment Job Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs overflow-y-auto"
            @keydown.escape.window="$wire.closeCreate()">
            <div class="relative w-full max-w-3xl rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-gray-800 dark:bg-gray-900 my-8">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Create Operational Shipment Job</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Initiate freight job with routing, cargo specifications, and container allocation</p>
                    </div>
                    <button type="button" wire:click="closeCreate" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-lg">&times;</button>
                </div>

                <form wire:submit="createShipment" class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Customer -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Customer (Shipper / Consignee) *</label>
                            <select wire:model="customer_id" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="">-- Select Customer --</option>
                                @foreach ($this->customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->legal_name }} ({{ $c->partner_code }})</option>
                                @endforeach
                            </select>
                            @error('customer_id') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Mode & Direction -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Transport Mode *</label>
                            <select wire:model="transport_mode" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="OCEAN">OCEAN (Sea Freight)</option>
                                <option value="AIR">AIR (Air Freight)</option>
                                <option value="ROAD">ROAD (Inland Haulage)</option>
                                <option value="RAIL">RAIL (Railway)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Trade Direction *</label>
                            <select wire:model="direction" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="OUTBOUND">OUTBOUND (Export)</option>
                                <option value="INBOUND">INBOUND (Import)</option>
                                <option value="DOMESTIC">DOMESTIC</option>
                            </select>
                        </div>

                        <!-- Origin & Destination -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Origin Port / Location</label>
                            <select wire:model="origin_location_id" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="">-- Select Origin Location --</option>
                                @foreach ($this->locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Destination Port / Location</label>
                            <select wire:model="destination_location_id" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="">-- Select Destination Location --</option>
                                @foreach ($this->locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Carrier & Vessel -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Carrier (Shipping Line / Airline)</label>
                            <select wire:model="carrier_partner_id" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="">-- Select Carrier --</option>
                                @foreach ($this->carriers as $cr)
                                    <option value="{{ $cr->id }}">{{ $cr->legal_name }} ({{ $cr->partner_code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Vessel / Flight & Voyage</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" wire:model="vessel_name" placeholder="Vessel Name..." class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                                <input type="text" wire:model="voyage_no" placeholder="Voyage / Flight #..." class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                            </div>
                        </div>

                        <!-- ETD & ETA -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">ETD (Estimated Departure)</label>
                            <input type="datetime-local" wire:model="etd" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">ETA (Estimated Arrival)</label>
                            <input type="datetime-local" wire:model="eta" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                        </div>
                    </div>

                    <!-- Cargo & Container section -->
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-2">Initial Cargo & Container Allocation</h4>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="sm:col-span-3">
                                <input type="text" wire:model="cargo_description" placeholder="Cargo description (e.g. Garments, Auto parts)..." class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                            </div>
                            <div>
                                <input type="text" wire:model="container_number" placeholder="Container # (e.g. ONEU1234567)" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 font-mono focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                            </div>
                            <div>
                                <input type="text" wire:model="seal_number" placeholder="Seal # (e.g. SL987654)" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 font-mono focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                            </div>
                            <div>
                                <input type="number" step="0.01" wire:model="gross_weight_kg" placeholder="Gross Wt (KG)" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="closeCreate" class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600">
                            Create Shipment Job
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Record Milestone Event Modal -->
    @if ($showEventModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs"
            @keydown.escape.window="$wire.closeEventModal()">
            <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white/90">Log Operational Milestone Event</h3>
                    <button type="button" wire:click="closeEventModal" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
                </div>

                <form wire:submit="recordEvent" class="mt-4 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Milestone Event *</label>
                        <select wire:model="event_code" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                            <option value="BOOKING_CONFIRMED">BOOKING_CONFIRMED (Carrier Confirmed)</option>
                            <option value="CARGO_RECEIVED">CARGO_RECEIVED (Warehouse / Terminal Gate-in)</option>
                            <option value="CUSTOMS_CLEARED">CUSTOMS_CLEARED (Export / Import Cleared)</option>
                            <option value="DEPARTED">DEPARTED (Vessel Sunk Anchor / Aircraft Took Off)</option>
                            <option value="ARRIVED">ARRIVED (Arrived at Destination Terminal)</option>
                            <option value="DELIVERED">DELIVERED (Handed over to Consignee / POD)</option>
                            <option value="EXCEPTION">EXCEPTION (Delay / Roll-over / Hold)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Actual Timestamp</label>
                        <input type="datetime-local" wire:model="event_actual_at" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Operational Notes / Reason</label>
                        <textarea wire:model="event_notes" rows="2" placeholder="e.g. Container gated in at Cat Lai port yard..." class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="closeEventModal" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-1.5 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600">
                            Log Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Shipment Detail Workbench Slide-over Drawer -->
    @if ($showDetailDrawer && $this->detailShipment)
        @php
            $sh = $this->detailShipment;
            $milestones = [
                'BOOKING_PENDING' => '1. Booking Pending',
                'BOOKING_CONFIRMED' => '2. Booking Confirmed',
                'CARGO_RECEIVED' => '3. Cargo Received',
                'IN_TRANSIT' => '4. In Transit',
                'ARRIVED' => '5. Arrived',
                'DELIVERED' => '6. Delivered',
            ];
            $currentStatus = $sh->operational_status ?? 'BOOKING_PENDING';
        @endphp
        <div class="fixed inset-0 z-50 overflow-hidden bg-gray-900/50 backdrop-blur-xs"
            @keydown.escape.window="$wire.closeDetail()">
            <div class="fixed inset-y-0 end-0 flex max-w-full ps-10">
                <div class="w-screen max-w-3xl bg-white shadow-2xl dark:bg-gray-900 flex flex-col">
                    <!-- Drawer Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-bold font-mono text-brand-600 dark:text-brand-400">
                                    {{ $sh->shipment_number }}
                                </h3>
                                <span class="rounded bg-brand-50 dark:bg-brand-950/40 text-brand-600 px-2 py-0.5 text-xs font-medium">
                                    {{ $sh->transport_mode }} • {{ $sh->direction }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Customer: <strong>{{ $sh->customer?->legal_name }}</strong>
                            </p>
                        </div>
                        <button type="button" wire:click="closeDetail" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Drawer Content -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        <!-- Operational Milestone Timeline Stepper -->
                        <div class="rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-white/2 p-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                                Milestone Progress Tracker
                            </h4>
                            <div class="flex items-center justify-between overflow-x-auto pb-1">
                                @php
                                    $statuses = ['BOOKING_PENDING', 'BOOKING_CONFIRMED', 'CARGO_RECEIVED', 'IN_TRANSIT', 'ARRIVED', 'DELIVERED'];
                                    $currentIdx = array_search($currentStatus, $statuses);
                                    if ($currentIdx === false) $currentIdx = 0;
                                @endphp
                                @foreach ($statuses as $idx => $stKey)
                                    @php
                                        $isCompleted = $idx <= $currentIdx;
                                        $isCurrent = $idx === $currentIdx;
                                    @endphp
                                    <div class="flex flex-col items-center min-w-16">
                                        <div class="size-7 rounded-full flex items-center justify-center text-xs font-bold {{ $isCurrent ? 'bg-brand-500 text-white ring-4 ring-brand-100 dark:ring-brand-950' : ($isCompleted ? 'bg-emerald-600 text-white' : 'border border-gray-300 dark:border-gray-700 text-gray-400') }}">
                                            @if ($isCompleted && !$isCurrent) &check; @else {{ $idx + 1 }} @endif
                                        </div>
                                        <span class="text-[10px] text-center font-medium mt-1 {{ $isCurrent ? 'text-brand-600 font-bold' : ($isCompleted ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400') }}">
                                            {{ str_replace('_', ' ', $stKey) }}
                                        </span>
                                    </div>
                                    @if (!$loop->last)
                                        <div class="flex-1 h-0.5 mx-1 {{ $idx < $currentIdx ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-800' }}"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Routing & Carrier Details -->
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-gray-400">Carrier / Line:</span>
                                <div class="font-bold text-gray-800 dark:text-white/90 mt-0.5">
                                    {{ $sh->carrier?->legal_name ?? 'To Be Advised' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-400">Vessel & Voyage:</span>
                                <div class="font-bold text-gray-800 dark:text-white/90 mt-0.5">
                                    {{ $sh->vessel_name ?? '—' }} {{ $sh->voyage_no ? "({$sh->voyage_no})" : '' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-400">Master B/L (MBL):</span>
                                <div class="font-mono font-medium text-gray-800 dark:text-white/90 mt-0.5">
                                    {{ $sh->master_bl_no ?? 'Pending' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-400">House B/L (HBL):</span>
                                <div class="font-mono font-medium text-gray-800 dark:text-white/90 mt-0.5">
                                    {{ $sh->house_bl_no ?? 'Pending' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-400">Port of Loading (POL):</span>
                                <div class="font-medium text-gray-800 dark:text-white/90 mt-0.5">
                                    {{ $sh->originLocation?->name ?? 'Standard Port' }}
                                </div>
                                <div class="text-[10px] text-gray-400">ETD: {{ $sh->etd ? $sh->etd->format('d/m/Y H:i') : 'TBD' }}</div>
                            </div>
                            <div>
                                <span class="text-gray-400">Port of Discharge (POD):</span>
                                <div class="font-medium text-gray-800 dark:text-white/90 mt-0.5">
                                    {{ $sh->destinationLocation?->name ?? 'Standard Port' }}
                                </div>
                                <div class="text-[10px] text-gray-400">ETA: {{ $sh->eta ? $sh->eta->format('d/m/Y H:i') : 'TBD' }}</div>
                            </div>
                        </div>

                        <!-- Cargo Items / Containers Table -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Cargo & Containers ({{ $sh->cargoItems->count() }})
                                </h4>
                            </div>
                            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-50/75 dark:bg-white/2 text-gray-500">
                                        <tr>
                                            <th class="py-2.5 px-3 text-start">Container / Seal #</th>
                                            <th class="py-2.5 px-3 text-start">Description</th>
                                            <th class="py-2.5 px-3 text-center">Packages</th>
                                            <th class="py-2.5 px-3 text-end">Gross Wt (KG)</th>
                                            <th class="py-2.5 px-3 text-end">Volume (CBM)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                        @forelse ($sh->cargoItems as $item)
                                            <tr>
                                                <td class="py-2 px-3 font-mono font-medium text-gray-800 dark:text-white/90">
                                                    {{ $item->container_number ?? 'LCL / Cargo' }}
                                                    @if ($item->seal_number)
                                                        <span class="text-gray-400 block text-[10px]">Seal: {{ $item->seal_number }}</span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-3 text-gray-700 dark:text-gray-300">{{ $item->description }}</td>
                                                <td class="py-2 px-3 text-center">{{ number_format((float)$item->package_count, 0) }} {{ $item->package_type_code ?? 'PKG' }}</td>
                                                <td class="py-2 px-3 text-end font-mono">{{ number_format((float)$item->gross_weight_kg, 2) }}</td>
                                                <td class="py-2 px-3 text-end font-mono">{{ number_format((float)$item->volume_cbm, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-3 px-3 text-center text-gray-400">No cargo items allocated yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Milestone Audit Trail Timeline -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Operational Event Log & Milestones
                                </h4>
                                <button type="button" wire:click="openEventModal({{ $sh->id }})" class="text-xs text-brand-500 hover:text-brand-600 font-medium">
                                    + Log Event
                                </button>
                            </div>
                            <div class="rounded-xl border border-gray-100 dark:border-gray-800 p-3 space-y-3">
                                @forelse ($sh->events as $ev)
                                    <div class="flex items-start gap-3 text-xs">
                                        <div class="size-2 rounded-full bg-brand-500 mt-1.5 shrink-0"></div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-gray-800 dark:text-white/90 font-mono">{{ $ev->event_code }}</span>
                                                <span class="text-[11px] text-gray-400">{{ $ev->actual_at?->format('d/m/Y H:i') }}</span>
                                            </div>
                                            @if ($ev->notes)
                                                <p class="text-gray-600 dark:text-gray-400 text-[11px] mt-0.5">{{ $ev->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-2 text-xs text-gray-400">No milestone events logged yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Drawer Footer -->
                    <div class="p-6 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-3">
                        <button type="button" wire:click="closeDetail" class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300">
                            Close
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="openEventModal({{ $sh->id }})" class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                                Log Custom Event
                            </button>

                            @if ($sh->operational_status === 'BOOKING_PENDING')
                                <button type="button" wire:click="advanceMilestone({{ $sh->id }}, 'BOOKING_CONFIRMED')" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-medium text-white hover:bg-blue-700 shadow-theme-xs">
                                    Confirm Carrier Booking &rarr;
                                </button>
                            @elseif ($sh->operational_status === 'BOOKING_CONFIRMED')
                                <button type="button" wire:click="advanceMilestone({{ $sh->id }}, 'CARGO_RECEIVED')" class="rounded-lg bg-purple-600 px-4 py-2 text-xs font-medium text-white hover:bg-purple-700 shadow-theme-xs">
                                    Receive Cargo into Depot &rarr;
                                </button>
                            @elseif ($sh->operational_status === 'CARGO_RECEIVED')
                                <button type="button" wire:click="advanceMilestone({{ $sh->id }}, 'DEPARTED')" class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700 shadow-theme-xs">
                                    Mark Departed (ETD) &rarr;
                                </button>
                            @elseif ($sh->operational_status === 'IN_TRANSIT')
                                <button type="button" wire:click="advanceMilestone({{ $sh->id }}, 'ARRIVED')" class="rounded-lg bg-teal-600 px-4 py-2 text-xs font-medium text-white hover:bg-teal-700 shadow-theme-xs">
                                    Mark Arrived at Port &rarr;
                                </button>
                            @elseif ($sh->operational_status === 'ARRIVED')
                                <button type="button" wire:click="advanceMilestone({{ $sh->id }}, 'DELIVERED')" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-medium text-white hover:bg-emerald-700 shadow-theme-xs">
                                    Deliver Cargo & Complete Job &rarr;
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
