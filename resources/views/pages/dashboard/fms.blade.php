@extends('layouts.app')

@section('content')
  <div class="space-y-6">
    <!-- Breadcrumb / Header Title -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">
          Freight Operations Overview
        </h2>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Live monitoring of global logistics jobs, container shipments, docs, and billing
        </p>
      </div>

      <div class="flex items-center gap-2">
        <a href="/operations/shipments" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-brand-600 transition">
          <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          <span>Create New Shipment</span>
        </a>
      </div>
    </div>

    <!-- 4 Key Logistics KPI Cards -->
    <x-main.dashboard.fms.metrics />

    <!-- Milestone Pipeline -->
    <x-main.dashboard.fms.pipeline />

    <!-- Two-column: Freight Mode Progress & Milestone Alerts -->
    <div class="grid grid-cols-12 gap-6">
      <div class="col-span-12 xl:col-span-7">
        <x-main.dashboard.fms.volume-chart />
      </div>
      <div class="col-span-12 xl:col-span-5">
        <x-main.dashboard.fms.alerts />
      </div>
    </div>

    <!-- Recent Shipments Table -->
    <x-main.dashboard.fms.recent-shipments />
  </div>
@endsection
