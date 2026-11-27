@php
$recentShipments = [
    [
        'job_no' => 'SH26-000108',
        'customer' => 'Samsung Electronics VN',
        'mode' => 'Ocean Export FCL',
        'mode_badge' => 'brand',
        'details' => '4x40HC · High-tech Equipment',
        'origin' => 'Cat Lai, VNSGN',
        'dest' => 'Los Angeles, USLAX',
        'carrier' => 'ONE Line',
        'etd' => '28 Nov 2026',
        'status' => 'In Transit',
        'status_color' => 'blue',
        'profit' => '18,500,000 ₫'
    ],
    [
        'job_no' => 'SH26-000107',
        'customer' => 'VinFast Auto LLC',
        'mode' => 'Ocean Import FCL',
        'mode_badge' => 'purple',
        'details' => '12x40GP · Auto Components',
        'origin' => 'Busan, KRPUS',
        'dest' => 'Hai Phong, VNHPH',
        'carrier' => 'Sinokor',
        'etd' => '29 Nov 2026',
        'status' => 'Customs Cleared',
        'status_color' => 'green',
        'profit' => '34,200,000 ₫'
    ],
    [
        'job_no' => 'SH26-000106',
        'customer' => 'LG Innotek Hai Phong',
        'mode' => 'Air Freight',
        'mode_badge' => 'orange',
        'details' => '1,850 kg · Optical Sensors',
        'origin' => 'Tan Son Nhat, VVTS',
        'dest' => 'Incheon, RKSI',
        'carrier' => 'Vietnam Airlines',
        'etd' => '27 Nov 2026',
        'status' => 'On Board',
        'status_color' => 'blue',
        'profit' => '12,800,000 ₫'
    ],
    [
        'job_no' => 'SH26-000105',
        'customer' => 'Foxconn Vietnam',
        'mode' => 'Ocean Export LCL',
        'mode_badge' => 'brand',
        'details' => '18.5 CBM · PCB Boards',
        'origin' => 'Hai Phong, VNHPH',
        'dest' => 'Singapore, SGSIN',
        'carrier' => 'Evergreen',
        'etd' => '30 Nov 2026',
        'status' => 'Booked',
        'status_color' => 'amber',
        'profit' => '8,600,000 ₫'
    ],
    [
        'job_no' => 'SH26-000104',
        'customer' => 'Intel Products VN',
        'mode' => 'Air Freight',
        'mode_badge' => 'orange',
        'details' => '420 kg · Microprocessors',
        'origin' => 'Tan Son Nhat, VVTS',
        'dest' => 'Chicago O\'Hare, KORD',
        'carrier' => 'Korean Air Cargo',
        'etd' => '28 Nov 2026',
        'status' => 'In Transit',
        'status_color' => 'blue',
        'profit' => '9,400,000 ₫'
    ],
    [
        'job_no' => 'SH26-000103',
        'customer' => 'Formosa Plastics',
        'mode' => 'Inland Haulage',
        'mode_badge' => 'gray',
        'details' => '2x40DC · Raw Resin',
        'origin' => 'Cat Lai, VNSGN',
        'dest' => 'Dong Nai Industrial Zone',
        'carrier' => 'Dang Dung Trucking',
        'etd' => '26 Nov 2026',
        'status' => 'Completed / POD',
        'status_color' => 'green',
        'profit' => '3,500,000 ₫'
    ],
];
@endphp

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pt-5 pb-4 sm:px-6 dark:border-gray-800 dark:bg-white/3">
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                Recent Shipments (Jobs)
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Latest active freight operations across Ocean, Air and Haulage
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="/operations/shipments" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-xs hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                <span>View All Jobs</span>
                <svg class="size-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="/operations/shipments" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white shadow-xs hover:bg-brand-600 transition">
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>New Job</span>
            </a>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto">
        <table class="min-w-full text-start text-xs">
            <thead class="border-y border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-white/[0.02]">
                <tr>
                    <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Job No / Customer</th>
                    <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Mode & Volume</th>
                    <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Routing</th>
                    <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Carrier / Schedule</th>
                    <th class="py-3 px-3 text-start font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="py-3 px-3 text-end font-semibold text-gray-600 dark:text-gray-300">Est. Profit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($recentShipments as $shipment)
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                        <!-- Job No / Customer -->
                        <td class="py-3 px-3">
                            <a href="/operations/shipments" class="font-semibold text-brand-600 hover:underline dark:text-brand-400">
                                {{ $shipment['job_no'] }}
                            </a>
                            <p class="mt-0.5 text-gray-700 dark:text-gray-300 font-medium">
                                {{ $shipment['customer'] }}
                            </p>
                        </td>

                        <!-- Mode & Volume -->
                        <td class="py-3 px-3">
                            <span class="inline-block rounded px-1.5 py-0.5 text-[11px] font-medium 
                                @if($shipment['mode_badge'] === 'brand') bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400
                                @elseif($shipment['mode_badge'] === 'purple') bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400
                                @elseif($shipment['mode_badge'] === 'orange') bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400
                                @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                                {{ $shipment['mode'] }}
                            </span>
                            <p class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">
                                {{ $shipment['details'] }}
                            </p>
                        </td>

                        <!-- Routing -->
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-1.5 font-medium text-gray-800 dark:text-gray-200">
                                <span>{{ $shipment['origin'] }}</span>
                                <span class="text-gray-400 rtl:rotate-180">→</span>
                                <span>{{ $shipment['dest'] }}</span>
                            </div>
                        </td>

                        <!-- Carrier / Schedule -->
                        <td class="py-3 px-3">
                            <p class="font-medium text-gray-700 dark:text-gray-300">
                                {{ $shipment['carrier'] }}
                            </p>
                            <p class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">
                                ETD: {{ $shipment['etd'] }}
                            </p>
                        </td>

                        <!-- Status -->
                        <td class="py-3 px-3">
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium
                                @if($shipment['status_color'] === 'blue') bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400
                                @elseif($shipment['status_color'] === 'green') bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400
                                @elseif($shipment['status_color'] === 'amber') bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400
                                @else bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 @endif">
                                <span class="size-1.5 rounded-full 
                                    @if($shipment['status_color'] === 'blue') bg-blue-500
                                    @elseif($shipment['status_color'] === 'green') bg-emerald-500
                                    @elseif($shipment['status_color'] === 'amber') bg-amber-500
                                    @else bg-gray-400 @endif"></span>
                                {{ $shipment['status'] }}
                            </span>
                        </td>

                        <!-- Profit -->
                        <td class="py-3 px-3 text-end font-semibold text-emerald-600 dark:text-emerald-400">
                            {{ $shipment['profit'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
