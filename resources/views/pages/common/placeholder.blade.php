@extends('layouts.app')

@section('content')
  <div class="space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 dark:border-gray-800 dark:bg-white/3">
      <div class="max-w-2xl">
        <div class="inline-flex items-center gap-2 rounded-lg bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 mb-4">
          <span class="size-2 rounded-full bg-brand-500"></span>
          <span>Cargo FMS Core Module</span>
        </div>

        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">
          {{ $title }}
        </h3>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
          {{ $description ?? 'This module manages freight forwarding workflows and is linked to the Core Database Tables established in Phase 1 (Company/Branch multi-tenancy, Business Partners, Service Types, and Numbering Sequences).' }}
        </p>

        @if(isset($tables) && count($tables) > 0)
          <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              Underlying Database Tables:
            </span>
            <div class="mt-2 flex flex-wrap gap-2">
              @foreach($tables as $tbl)
                <span class="rounded-md bg-white px-2.5 py-1 text-xs font-mono font-medium text-gray-700 shadow-2xs border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                  {{ $tbl }}
                </span>
              @endforeach
            </div>
          </div>
        @endif

        <div class="mt-6 flex flex-wrap items-center gap-3">
          <a href="/dashboard" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-brand-600 transition">
            <svg class="size-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Dashboard</span>
          </a>
          <a href="/master/partners" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-700 shadow-xs hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
            <span>View Business Partners</span>
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection
