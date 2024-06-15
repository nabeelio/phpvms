@extends('app')
@section('title', __('common.dashboard'))

@section('content')

@php
  $pilots = cache()->remember('pilots', 3600, function () {
    return \App\Models\User::where('state', UserState::ACTIVE)->count();
  });
  $flight_time = cache()->remember('flighttime', 3600, function () {
    return \App\Models\Pirep::where('state', PirepState::ACCEPTED)->sum('flight_time');
  });
  $flights = cache()->remember('flights', 3600, function () {
    return \App\Models\Pirep::where('state', PirepState::ACCEPTED)->count();
  });
  $schedules = cache()->remember('schedules', 3600, function () {
    return \App\Models\Flight::where('active', 1)->count();
  });
@endphp

  <!-- PILOT STATISTICS -->
  <div id="pilot__statistics" class="w-full shadow-sm">
    <div class="flex flex-col bg-white rounded-sm">
      <div id="pilotStatsHead" class="flex border-b border-gray-100 p-4">
        <h2 class="text-xl font-medium">Your Statistics</h2>
      </div>
      <div id="pilotStatsBody" class="flex flex-row text-center items-center p-4 divide-x">
        <div class="w-3/12">
          <h2 class="text-2xl">{{ $user->flights }}</h2>
          <h6 class="text-base font-medium">{{ trans_choice('common.flight', $user->flights) }}</h6>
        </div>
        <div class="w-3/12">
          <h2 class="text-2xl">@minutestotime($user->flight_time)</h2>
          <h6 class="text-base font-medium">@lang('dashboard.totalhours')</h6>
        </div>
        <div class="w-3/12">
          <h2 class="text-2xl">{{ optional($user->journal)->balance ?? 0 }}</h2>
          <h6 class="text-base font-medium">@lang('dashboard.yourbalance')</h6>
        </div>
        <div class="w-3/12">
          <h2 class="text-2xl">{{ $current_airport }}</h2>
          <h6 class="text-base font-medium">@lang('airports.current')</h6>
        </div>
      </div>
    </div>
  </div>
  <!-- PILOT STATISTICS END -->

  <div id="content" class="w-full flex gap-8 mt-8">
    <div class="w-8/12 flex flex-col self-start">
      <div id="pilotReports" class="bg-white shadow-sm">
        <div id="pilotReports_head" class="p-4 border-b border-gray-100">
          <h2 class="text-xl font-medium">Latest PIREPs</h2>
          <h6 class="text-sm text-gray-500">Your 3 most recent flights</h6>
        </div>
        <div id="pilotReports_body">
          <table class="table-auto w-full">
            <thead class="bg-blue-900">
              <th class="text-base text-white font-medium px-2 py-3">Flight Number</th>
              <th class="text-base text-white font-medium px-2 py-3">Departure</th>
              <th class="text-base text-white font-medium px-2 py-3">Arrival</th>
              <th class="text-base text-white font-medium px-2 py-3">Flight Time / Distance</th>
              <th class="text-base text-white font-medium px-2 py-3">Status</th>
              <th class="min-w-12"></th>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @if($last_pirep != null)
                @include('dashboard.pirep_card', ['pireps' => $latest_pireps])
              @endif
            </tbody>
          </table>
          @if($last_pirep == null)
            <div class="flex justify-center p-4">
              <span>No PIREPS filed yet...</span>
            </div>
          @endif
        </div>
      </div>
      {{ Widget::latestNews(['count' => 3]) }}
    </div>
    <div class="w-4/12 flex flex-col self-start">
      <div id="airlineStats" class="bg-white shadow-sm">
        <div id="airlineStats_head" class="border-b border-gray-100 p-4">
          <h2 class="text-xl font-medium">SASva Statistics</h2>
        </div>
        <div id="airlineStats_body">
          <div class="flex flex-col divide-y divide-gray-100">
            <div class="flex divide-x divide-gray-100">
              <div class="w-1/2 p-2">
                <h2 class="text-xl text-center">{{ $pilots }}</h2>
                <h6 class="text-base text-center font-medium">Active Pilots</h6>
              </div>
              <div class="w-1/2 p-2">
                <h2 class="text-xl text-center">{{ $flights }}</h2>
                <h6 class="text-base text-center font-medium">Pireps Filed</h6>
              </div>
            </div>
            <div class="flex divide-x divide-gray-100">
              <div class="w-1/2 p-2">
                <h2 class="text-xl text-center">@minutestotime($flight_time)</h2>
                <h6 class="text-base text-center font-medium">Time Flown</h6>
              </div>
              <div class="w-1/2 p-2">
                <h2 class="text-xl text-center">{{ $schedules }}</h2>
                <h6 class="text-base text-center font-medium">Scheduled Flights</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{ Widget::Weather(['icao' => $current_airport]) }}
    </div>
  </div>

  <div class="row">
    <div class="col-sm-8">

      @if(Auth::user()->state === \App\Models\Enums\UserState::ON_LEAVE)
        <div class="row">
          <div class="col-sm-12">
            <div class="alert alert-warning" role="alert">
              You are on leave! File a PIREP to set your status to active!
            </div>
          </div>
        </div>
      @endif

      

    </div>
  </div>
@endsection
