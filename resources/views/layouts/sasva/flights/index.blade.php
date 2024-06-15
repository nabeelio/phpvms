@extends('app')
@section('title', trans_choice('common.flight', 2))

@section('content')
  <div id="content" class="w-full flex gap-8">
    <div class="w-9/12 flex flex-col self-start">
      <div id="flights" class="bg-white shadow-sm">
        <div id="flights__head" class="p-4 border-b border-gray-100">
          <h2 class="text-xl font-medium">Flights</h2>
          <h6 class="text-sm text-gray-500">Find your next flight</h6>
        </div>
        <div id="flights__body">
          <table class="table-auto w-full">
            <thead class="bg-blue-900">
              <th class="text-base text-white text-center font-medium px-4 py-3">Flight Number</th>
              <th class="text-base text-white text-left font-medium px-4 py-3">Departure</th>
              <th class="text-base text-white text-left font-medium px-4 py-3">Arrival</th>
              <th class="text-base text-white text-center font-medium px-4 py-3">Flight Time / Distance</th>
              <th class="min-w-12"></th>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @include('flights.table')
            </tbody>
          </table>
        </div>
        <div id="flights__footer">
          {{ $flights->withQueryString()->links('pagination.default') }}
        </div>
      </div>
    </div>
    <div class="w-3/12 flex flex-col self-start">
      <div id="flightSearch" class="bg-white shadow-sm">
        <div id="flightSearch__head" class="p-4 border-b border-gray-100">
          <h2 class="text-xl font-medium">Search</h2>
          <h6 class="text-sm text-gray-500">Search for specific flights</h6>
        </div>
        <div id="flightSearch__body">
          @include('flights.search')
        </div>
      </div>
    </div>
  </div>
@endsection

@include('flights.scripts')

