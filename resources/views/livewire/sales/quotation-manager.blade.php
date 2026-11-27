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

    <!-- 4 Workflow KPI Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 md:gap-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Quotations</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                    {{ \App\Models\Quotation::count() }}
                </h4>
                <span class="text-xs text-brand-600 dark:text-brand-400 font-medium">Stage 1: Pricing</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Draft & Active Quotes</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                    {{ \App\Models\Quotation::whereIn('status', ['DRAFT', 'SENT'])->count() }}
                </h4>
                <span class="text-xs text-amber-600 font-medium">Pending Acceptance</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Sales Orders Created</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ \App\Models\SalesOrder::count() }}
                </h4>
                <span class="text-xs text-blue-600 font-medium">Stage 2: Booked</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Shipments Converted</span>
            <div class="mt-2 flex items-baseline justify-between">
                <h4 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                    {{ \App\Models\Shipment::count() }}
                </h4>
                <span class="text-xs text-emerald-600 font-medium">Stage 3: Operations</span>
            </div>
        </div>
    </div>

    <!-- Main Quotations Table -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/3">
        <!-- Toolbar -->
        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                <!-- Search Input -->
                <div class="relative w-full sm:w-72">
                    <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 text-gray-400">
                        <x-svg.search class="size-4" />
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search quote # or customer..."
                        class="w-full rounded-lg border border-gray-200 bg-transparent py-2 pe-3 ps-9 text-xs text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:text-white/90" />
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-40">
                    <select wire:model.live="selectedStatus"
                        class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-700 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                        <option value="ALL">All Status</option>
                        <option value="DRAFT">Draft</option>
                        <option value="SENT">Sent</option>
                        <option value="ACCEPTED">Accepted</option>
                        <option value="REJECTED">Rejected</option>
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

            <!-- Create Quotation Button -->
            <button type="button" wire:click="openCreate"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-hidden">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Quotation</span>
            </button>
        </div>

        <!-- Quotations Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs text-gray-700 dark:text-gray-300">
                <thead class="border-b border-gray-100 bg-gray-50/75 text-gray-500 dark:border-gray-800 dark:bg-white/2 dark:text-gray-400">
                    <tr>
                        <th class="py-3 px-4 text-start font-semibold">Quote Number</th>
                        <th class="py-3 px-4 text-start font-semibold">Customer (Partner)</th>
                        <th class="py-3 px-4 text-start font-semibold">Mode / Direction</th>
                        <th class="py-3 px-4 text-end font-semibold">Sell Amount</th>
                        <th class="py-3 px-4 text-center font-semibold">Status</th>
                        <th class="py-3 px-4 text-center font-semibold">Downstream Workflow</th>
                        <th class="py-3 px-4 text-end font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($this->quotations as $quote)
                        @php
                            $rev = $quote->revisions->where('revision_number', $quote->current_revision_number)->first() ?? $quote->revisions->first();
                            $so = $quote->salesOrder;
                            $shipment = $so?->shipment ?? $quote->shipment;
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/2 transition-colors">
                            <!-- Quote Number -->
                            <td class="py-3.5 px-4 font-mono font-medium text-brand-600 dark:text-brand-400">
                                <button type="button" wire:click="openDetail({{ $quote->id }})" class="hover:underline">
                                    {{ $quote->quotation_number }}
                                </button>
                                <span class="ms-1 text-[10px] text-gray-400">R{{ $quote->current_revision_number }}</span>
                            </td>

                            <!-- Customer -->
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-gray-800 dark:text-white/90">
                                    {{ $quote->customer?->legal_name ?? '—' }}
                                </div>
                                <div class="text-[11px] text-gray-400 font-mono">
                                    {{ $quote->customer?->partner_code }}
                                </div>
                            </td>

                            <!-- Mode / Direction -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5 font-medium text-gray-800 dark:text-white/90">
                                    <span>{{ $quote->transport_mode }}</span>
                                    <span class="text-gray-400">•</span>
                                    <span>{{ $quote->direction }}</span>
                                </div>
                                <div class="text-[11px] text-gray-400">
                                    Valid until: {{ $quote->valid_until ? $quote->valid_until->format('d/m/Y') : 'Open' }}
                                </div>
                            </td>

                            <!-- Sell Amount & Profit -->
                            <td class="py-3.5 px-4 text-end font-mono">
                                <div class="font-semibold text-gray-800 dark:text-white/90">
                                    {{ number_format((float) ($rev?->total_sell_amount ?? 0), 2) }} {{ $rev?->currency?->code ?? 'USD' }}
                                </div>
                                <div class="text-[11px] text-emerald-600 dark:text-emerald-400">
                                    Est. Profit: +{{ number_format((float) ($rev?->est_gross_profit ?? 0), 2) }}
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4 text-center">
                                @if ($quote->status === 'ACCEPTED')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span> Accepted
                                    </span>
                                @elseif ($quote->status === 'SENT')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-[11px] font-semibold text-blue-700 dark:bg-blue-950/40 dark:text-blue-400">
                                        <span class="size-1.5 rounded-full bg-blue-500"></span> Sent
                                    </span>
                                @elseif ($quote->status === 'REJECTED')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-[11px] font-semibold text-rose-700 dark:bg-rose-950/40 dark:text-rose-400">
                                        <span class="size-1.5 rounded-full bg-rose-500"></span> Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-semibold text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                                        <span class="size-1.5 rounded-full bg-amber-500"></span> Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Downstream Workflow Tracking -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5 text-[11px]">
                                    @if ($so)
                                        <span class="font-mono font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/30 px-2 py-0.5 rounded" title="Sales Order Number">
                                            {{ $so->sales_order_number }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">No SO</span>
                                    @endif

                                    <span class="text-gray-300 dark:text-gray-700">&rarr;</span>

                                    @if ($shipment)
                                        <span class="font-mono font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-2 py-0.5 rounded" title="Operational Shipment Number">
                                            {{ $shipment->shipment_number }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">No Job</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" wire:click="openDetail({{ $quote->id }})"
                                        class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5"
                                        title="View Details">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    @if (!$so)
                                        <button type="button" wire:click="acceptQuote({{ $quote->id }})"
                                            class="inline-flex items-center gap-1 rounded bg-blue-50 px-2 py-1 text-[11px] font-medium text-blue-600 hover:bg-blue-100 dark:bg-blue-950/40 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                            title="Accept & Create Sales Order">
                                            <span>Accept</span>
                                        </button>
                                    @elseif ($so && !$shipment)
                                        <button type="button" wire:click="convertOrderToShipment({{ $so->id }})"
                                            class="inline-flex items-center gap-1 rounded bg-emerald-50 px-2 py-1 text-[11px] font-medium text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-400 dark:hover:bg-emerald-900/50"
                                            title="Convert to Operational Shipment">
                                            <span>Convert to Job</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No quotations found. Click <strong class="text-brand-500 font-semibold cursor-pointer" wire:click="openCreate">New Quotation</strong> to generate your first quote.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->quotations->links() }}
        </div>
    </div>

    <!-- Create Quotation Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs overflow-y-auto"
            @keydown.escape.window="$wire.closeCreate()">
            <div class="relative w-full max-w-3xl rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-gray-800 dark:bg-gray-900 my-8">
                <!-- Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Create Freight Quotation</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Auto-numbered with multi-currency pricing and line items</p>
                    </div>
                    <button type="button" wire:click="closeCreate" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-lg">&times;</button>
                </div>

                <!-- Form -->
                <form wire:submit="createQuotation" class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Customer -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Customer (Business Partner) *</label>
                            <select wire:model="business_partner_id" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="">-- Select Customer --</option>
                                @foreach ($this->partners as $p)
                                    <option value="{{ $p->id }}">{{ $p->legal_name }} ({{ $p->partner_code }})</option>
                                @endforeach
                            </select>
                            @error('business_partner_id') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Transport Mode -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Transport Mode</label>
                            <select wire:model="transport_mode" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="OCEAN">OCEAN (Sea Freight)</option>
                                <option value="AIR">AIR (Air Freight)</option>
                                <option value="ROAD">ROAD (Inland Haulage)</option>
                                <option value="RAIL">RAIL (Railway)</option>
                            </select>
                        </div>

                        <!-- Direction -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Trade Direction</label>
                            <select wire:model="direction" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                <option value="OUTBOUND">OUTBOUND (Export)</option>
                                <option value="INBOUND">INBOUND (Import)</option>
                                <option value="DOMESTIC">DOMESTIC</option>
                            </select>
                        </div>

                        <!-- Currency -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Currency *</label>
                            <select wire:model="currency_id" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                                @foreach ($this->currencies as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Valid Until -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Valid Until *</label>
                            <input type="date" wire:model="valid_until" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                        </div>
                    </div>

                    <!-- Line Items Section -->
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Pricing Lines (Buy / Sell)</h4>
                            <button type="button" wire:click="addLine" class="text-xs font-medium text-brand-500 hover:text-brand-600">
                                + Add Charge Line
                            </button>
                        </div>

                        <div class="space-y-2">
                            @foreach ($lines as $index => $line)
                                <div class="grid grid-cols-12 gap-2 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-white/2 items-center">
                                    <div class="col-span-4">
                                        <input type="text" wire:model="lines.{{ $index }}.description" placeholder="Charge description..."
                                            class="w-full rounded border border-gray-200 bg-white dark:bg-gray-900 py-1.5 px-2 text-xs text-gray-800 dark:text-white/90 focus:border-brand-500 focus:outline-hidden" />
                                    </div>
                                    <div class="col-span-2">
                                        <input type="text" wire:model="lines.{{ $index }}.unit" placeholder="Unit (CONT/KGS)"
                                            class="w-full rounded border border-gray-200 bg-white dark:bg-gray-900 py-1.5 px-2 text-xs text-gray-800 dark:text-white/90 focus:border-brand-500 focus:outline-hidden" />
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" step="0.01" wire:model="lines.{{ $index }}.quantity" placeholder="Qty"
                                            class="w-full rounded border border-gray-200 bg-white dark:bg-gray-900 py-1.5 px-2 text-xs text-gray-800 dark:text-white/90 focus:border-brand-500 focus:outline-hidden" />
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" step="0.01" wire:model="lines.{{ $index }}.unit_price" placeholder="Sell Price"
                                            class="w-full rounded border border-gray-200 bg-white dark:bg-gray-900 py-1.5 px-2 text-xs text-gray-800 dark:text-white/90 focus:border-brand-500 focus:outline-hidden" />
                                    </div>
                                    <div class="col-span-1">
                                        <input type="number" step="0.01" wire:model="lines.{{ $index }}.cost_unit_price" placeholder="Cost"
                                            class="w-full rounded border border-gray-200 bg-white dark:bg-gray-900 py-1.5 px-2 text-xs text-gray-800 dark:text-white/90 focus:border-brand-500 focus:outline-hidden" />
                                    </div>
                                    <div class="col-span-1 text-end">
                                        <button type="button" wire:click="removeLine({{ $index }})" class="text-rose-500 hover:text-rose-700 text-sm font-bold">&times;</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks & Routing Notes</label>
                        <textarea wire:model="remarks" rows="2" class="w-full rounded-lg border border-gray-200 bg-transparent py-2 px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" placeholder="Optional internal routing notes or customer remarks..."></textarea>
                    </div>

                    <!-- Footer buttons -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="closeCreate" class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600">
                            Create & Issue Quotation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Detail & Workflow Slide-over Drawer -->
    @if ($showDetailDrawer && $this->detailQuotation)
        @php
            $quote = $this->detailQuotation;
            $rev = $quote->revisions->where('revision_number', $quote->current_revision_number)->first() ?? $quote->revisions->first();
            $so = $quote->salesOrder;
            $shipment = $so?->shipment ?? $quote->shipment;
        @endphp
        <div class="fixed inset-0 z-50 overflow-hidden bg-gray-900/50 backdrop-blur-xs"
            @keydown.escape.window="$wire.closeDetail()">
            <div class="fixed inset-y-0 end-0 flex max-w-full ps-10">
                <div class="w-screen max-w-2xl bg-white shadow-2xl dark:bg-gray-900 flex flex-col">
                    <!-- Drawer Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-bold font-mono text-brand-600 dark:text-brand-400">
                                    {{ $quote->quotation_number }}
                                </h3>
                                <span class="rounded bg-brand-50 dark:bg-brand-950/40 text-brand-600 px-2 py-0.5 text-xs font-medium">
                                    Rev {{ $quote->current_revision_number }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Created on {{ $quote->created_at?->format('d/m/Y H:i') }}
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
                        <!-- Sales to Shipment Pipeline Visual Stepper -->
                        <div class="rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-white/2 p-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                                End-to-End Pipeline Progress
                            </h4>
                            <div class="flex items-center justify-between">
                                <!-- Step 1: Quotation -->
                                <div class="flex flex-col items-center">
                                    <div class="size-8 rounded-full flex items-center justify-center text-xs font-bold bg-brand-500 text-white">
                                        1
                                    </div>
                                    <span class="text-[11px] font-semibold text-gray-800 dark:text-white/90 mt-1">Quotation</span>
                                    <span class="text-[10px] text-brand-600">{{ $quote->status }}</span>
                                </div>

                                <div class="flex-1 h-0.5 mx-2 {{ $so ? 'bg-brand-500' : 'bg-gray-200 dark:bg-gray-800' }}"></div>

                                <!-- Step 2: Sales Order -->
                                <div class="flex flex-col items-center">
                                    <div class="size-8 rounded-full flex items-center justify-center text-xs font-bold {{ $so ? 'bg-blue-600 text-white' : 'border border-gray-300 dark:border-gray-700 text-gray-400' }}">
                                        2
                                    </div>
                                    <span class="text-[11px] font-semibold text-gray-800 dark:text-white/90 mt-1">Sales Order</span>
                                    <span class="text-[10px] text-blue-600 font-mono">{{ $so ? $so->sales_order_number : 'Pending' }}</span>
                                </div>

                                <div class="flex-1 h-0.5 mx-2 {{ $shipment ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-800' }}"></div>

                                <!-- Step 3: Operational Shipment -->
                                <div class="flex flex-col items-center">
                                    <div class="size-8 rounded-full flex items-center justify-center text-xs font-bold {{ $shipment ? 'bg-emerald-600 text-white' : 'border border-gray-300 dark:border-gray-700 text-gray-400' }}">
                                        3
                                    </div>
                                    <span class="text-[11px] font-semibold text-gray-800 dark:text-white/90 mt-1">Shipment Job</span>
                                    <span class="text-[10px] text-emerald-600 font-mono">{{ $shipment ? $shipment->shipment_number : 'Pending' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Customer & Trade Parameters -->
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-gray-400">Customer:</span>
                                <div class="font-bold text-gray-800 dark:text-white/90 mt-0.5">
                                    {{ $quote->customer?->legal_name }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-400">Mode & Direction:</span>
                                <div class="font-bold text-gray-800 dark:text-white/90 mt-0.5">
                                    {{ $quote->transport_mode }} • {{ $quote->direction }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-400">Origin / Port:</span>
                                <div class="font-medium text-gray-700 dark:text-gray-300 mt-0.5">
                                    {{ $quote->originLocation?->name ?? 'Standard Depot' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-400">Destination:</span>
                                <div class="font-medium text-gray-700 dark:text-gray-300 mt-0.5">
                                    {{ $quote->destinationLocation?->name ?? 'Discharge Terminal' }}
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Breakdown Table -->
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                                Cost & Revenue Breakdown
                            </h4>
                            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-50/75 dark:bg-white/2 text-gray-500">
                                        <tr>
                                            <th class="py-2.5 px-3 text-start">Description</th>
                                            <th class="py-2.5 px-3 text-center">Unit</th>
                                            <th class="py-2.5 px-3 text-end">Qty</th>
                                            <th class="py-2.5 px-3 text-end">Sell Rate</th>
                                            <th class="py-2.5 px-3 text-end">Total Sell</th>
                                            <th class="py-2.5 px-3 text-end">Cost</th>
                                            <th class="py-2.5 px-3 text-end">Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                        @foreach ($rev?->lines ?? [] as $line)
                                            <tr>
                                                <td class="py-2 px-3 font-medium text-gray-800 dark:text-white/90">{{ $line->description }}</td>
                                                <td class="py-2 px-3 text-center text-gray-500">{{ $line->unit }}</td>
                                                <td class="py-2 px-3 text-end font-mono">{{ number_format((float)$line->quantity, 2) }}</td>
                                                <td class="py-2 px-3 text-end font-mono">{{ number_format((float)$line->unit_price, 2) }}</td>
                                                <td class="py-2 px-3 text-end font-mono font-semibold">{{ number_format((float)$line->sell_amount, 2) }}</td>
                                                <td class="py-2 px-3 text-end font-mono text-gray-500">{{ number_format((float)$line->cost_amount, 2) }}</td>
                                                <td class="py-2 px-3 text-end font-mono text-emerald-600 font-semibold">+{{ number_format((float)$line->profit_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50/50 dark:bg-white/2 font-semibold border-t border-gray-100 dark:border-gray-800">
                                        <tr>
                                            <td colspan="4" class="py-2.5 px-3 text-end">Totals:</td>
                                            <td class="py-2.5 px-3 text-end text-brand-600 font-mono">{{ number_format((float)($rev?->total_sell_amount ?? 0), 2) }}</td>
                                            <td class="py-2.5 px-3 text-end text-gray-500 font-mono">{{ number_format((float)($rev?->total_cost_amount ?? 0), 2) }}</td>
                                            <td class="py-2.5 px-3 text-end text-emerald-600 font-mono">+{{ number_format((float)($rev?->est_gross_profit ?? 0), 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Downstream Converted Objects Details -->
                        @if ($so || $shipment)
                            <div class="space-y-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Generated Records & References
                                </h4>

                                @if ($so)
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-blue-100 bg-blue-50/50 dark:border-blue-900/30 dark:bg-blue-950/20 text-xs">
                                        <div>
                                            <span class="font-bold text-blue-700 dark:text-blue-300">Sales Order</span>
                                            <div class="font-mono font-bold text-gray-800 dark:text-white/90 text-sm mt-0.5">{{ $so->sales_order_number }}</div>
                                            <span class="text-[11px] text-gray-500">Accepted at {{ $so->accepted_at?->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <span class="rounded bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5">
                                            {{ $so->status }}
                                        </span>
                                    </div>
                                @endif

                                @if ($shipment)
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-emerald-100 bg-emerald-50/50 dark:border-emerald-900/30 dark:bg-emerald-950/20 text-xs">
                                        <div>
                                            <span class="font-bold text-emerald-700 dark:text-emerald-300">Operational Shipment</span>
                                            <div class="font-mono font-bold text-gray-800 dark:text-white/90 text-sm mt-0.5">{{ $shipment->shipment_number }}</div>
                                            <span class="text-[11px] text-gray-500">Job Status: {{ $shipment->status }} ({{ $shipment->operational_status }})</span>
                                        </div>
                                        <span class="rounded bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5">
                                            ACTIVE JOB
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Drawer Action Footer -->
                    <div class="p-6 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-3">
                        <button type="button" wire:click="closeDetail" class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300">
                            Close
                        </button>

                        <div class="flex items-center gap-2">
                            @if (!$so)
                                <button type="button" wire:click="acceptQuote({{ $quote->id }})"
                                    class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-medium text-white shadow-theme-xs hover:bg-blue-700">
                                    Accept & Issue Sales Order
                                </button>
                                <button type="button" wire:click="quickConvertQuotationToShipment({{ $quote->id }})"
                                    class="rounded-lg bg-brand-500 px-4 py-2 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600">
                                    Instant Convert to Shipment &rarr;
                                </button>
                            @elseif ($so && !$shipment)
                                <button type="button" wire:click="convertOrderToShipment({{ $so->id }})"
                                    class="rounded-lg bg-emerald-600 px-5 py-2 text-xs font-medium text-white shadow-theme-xs hover:bg-emerald-700">
                                    Convert to Operational Shipment &rarr;
                                </button>
                            @else
                                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Shipment Successfully Created
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
