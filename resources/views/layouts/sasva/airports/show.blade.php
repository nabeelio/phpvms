@extends('app')
@section('title', $airport->full_name)

@section('content')

  <div id="airport__header" class="w-full shadow-sm">
    <div class="flex flex-col bg-white rounded-sm">
      <div id="airportTitle" class="flex border-b border-gray-100 p-4">
        <h2 class="text-xl font-medium">{{ $airport->full_name }}</h2>
      </div>
      <div id="airportMap" class="flex flex-col divide-x">
        {{ Widget::AirspaceMap(['width' => '100%', 'height' => '400px', 'lat' => $airport->lat, 'lon' => $airport->lon]) }}
      </div>
    </div>
  </div>

  <div id="content" class="w-full flex gap-8 mt-8">
    <div class="w-full md:w-8/12 flex flex-col self-start">
      <div id="airport__statistics" class="w-full shadow-sm">
        <div class="flex flex-col bg-white rounded-sm">
          <div id="airportStatsHead" class="flex border-b border-gray-100 p-4">
            <h2 class="text-xl font-medium">Airport Statistics</h2>
          </div>
          <div id="airportStatsBody" class="flex flex-row text-center items-center p-4 divide-x">
            <div class="w-3/12">
              <h2 class="text-2xl">{{ count($inbound_flights) }}</h2>
              <h6 class="text-base font-medium">Total Inbound Flights</h6>
            </div>
            <div class="w-3/12">
              <h2 class="text-2xl">{{ count($outbound_flights) }}</h2>
              <h6 class="text-base font-medium">Total Outbound Flights</h6>
            </div>
            <div class="w-3/12">
              <h2 class="text-2xl">N/A</h2>
              <h6 class="text-base font-medium">TODO</h6>
            </div>
            <div class="w-3/12">
              <h2 class="text-2xl">N/A</h2>
              <h6 class="text-base font-medium">TODO</h6>
            </div>
          </div>
        </div>
      </div>
      <div class="w-full flex flex-row gap-8 mt-8">
        <div id="airportInboundFlights" class="w-6/12 bg-white shadow-sm">
          <div id="airportInboundFlights_head" class="p-4 border-b border-gray-100">
            <h2 class="text-xl font-medium">Inbound flights</h2>
            <h6 class="text-sm text-gray-500">Flights flying into {{ $airport->icao }}</h6>
          </div>
          <div id="airportInboundFlights_body">
            <table class="table-auto w-full">
              <thead class="bg-blue-900">
                <th class="text-base text-white font-medium px-2 py-3">Flight Number</th>
                <th class="text-base text-white font-medium px-2 py-3">Departure Airport</th>
              </thead>
              <tbody class="divide-y divide-gray-100">
                @foreach($inbound_flights as $flight)
                  <tr>
                    <td class="text-base text-center py-3">
                      <a href="{{ route('frontend.flights.show', [$flight->id]) }}">
                        {{ $flight->ident }}
                      </a>
                    </td>
                    <td class="text-base text-center py-3">{{ optional($flight->dpt_airport)->name }}
                      (<a href="{{route('frontend.airports.show',
                              ['id' => $flight->dpt_airport_id])}}">{{$flight->dpt_airport_id}}</a>)
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            @if(!$inbound_flights)
              <div class="flex justify-center p-4">
                <span>@lang('flights.none')</span>
              </div>
            @endif
          </div>
        </div>

        <div id="airportOutboundFlights" class="w-6/12 bg-white shadow-sm">
          <div id="airportOutboundFlights_head" class="p-4 border-b border-gray-100">
            <h2 class="text-xl font-medium">Outbound flights</h2>
            <h6 class="text-sm text-gray-500">Flights flying from {{ $airport->icao }}</h6>
          </div>
          <div id="airportOutboundFlights_body">
            <table class="table-auto w-full">
              <thead class="bg-blue-900">
                <th class="text-base text-white font-medium px-2 py-3">Flight Number</th>
                <th class="text-base text-white font-medium px-2 py-3">Arrival Airport</th>
              </thead>
              <tbody class="divide-y divide-gray-100">
                @foreach($outbound_flights as $flight)
                  <tr>
                    <td class="text-base text-center py-3">
                      <a href="{{ route('frontend.flights.show', [$flight->id]) }}">
                        {{ $flight->ident }}
                      </a>
                    </td>
                    <td class="text-base text-center py-3">{{ optional($flight->arr_airport)->name }}
                      (<a href="{{route('frontend.airports.show',
                              ['id' => $flight->arr_airport_id])}}">{{$flight->arr_airport_id}}</a>)
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            @if(!$outbound_flights)
              <div class="flex justify-center p-4">
                <span>@lang('flights.none')</span>
              </div>
            @endif
          </div>
        </div>
      </div>
      
    </div>
    <div class="w-4/12 flex flex-col self-start">
      <div id="airportNotes" class="bg-white shadow-sm">
        <div id="airportNotes_head" class="border-b border-gray-100 p-4">
          <h2 class="text-xl font-medium">Airport notes</h2>
        </div>
        <div id="airportNotes_body" class="p-4">
          {!! $airport->notes !!}
        </div>
      </div>
      {{ Widget::Weather(['icao' => $airport->icao]) }}
    </div>
  </div>
@endsection
