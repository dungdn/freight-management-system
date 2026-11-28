@extends('layouts.app')

@section('content')
  <div class="space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <livewire:operations.shipment-manager />
  </div>
@endsection
