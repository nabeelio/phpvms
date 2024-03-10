@extends('app')
@section('title', __('common.dashboard'))

@section('content')

  <div class="pt-20 mb-4">
    <div class="bg-center bg-cover" style="background-image: url({{ public_asset('assets/sasva/media/landing_bg.jpg') }});">
      <div class="py-20 flex justify-center items-center flex-col" style="background-color: rgba(75, 85, 99, .8) !important">
        <h1 class="text-xl font-semibold text-white">Dashboard</h1>
        <h2 class="text-base text-white">Welcome to your dashboard</h2>
      </div>
    </div>
  </div>

  <div class="container mx-auto px-2">
    <!-- PILOT STATS -->
    <div id="pilotstats" class="mb-7">
      <div class="flex flex-col md:flex-row">
        <div class="w-full flex flex-col pb-2 md:pb-0 md:pr-8">
          <div class="bg-white rounded-md p-4">
            <span class="text-2xl font-semibold">{{ $user->flights }}</span>
            <p class="text-base">{{ trans_choice('common.flight', $user->flights) }}</p>
          </div>
        </div>
        <div class="w-full flex flex-col pb-2 md:pb-0 md:pr-8">
          <div class="bg-white rounded-md p-4">
            <span class="text-2xl font-semibold">@minutestotime($user->flight_time)</span>
            <p class="text-base">@lang('dashboard.totalhours')</p>
          </div>
        </div>
        <div class="w-full flex flex-col pb-2 md:pb-0 md:pr-8">
          <div class="bg-white rounded-md p-4">
            <span class="text-2xl font-semibold">{{ optional($user->journal)->balance ?? 0 }}</span>
            <p class="text-base">@lang('dashboard.yourbalance')</p>
          </div>
        </div>
        <div class="w-full flex flex-col">
          <div class="bg-white rounded-md p-4">
            <span class="text-2xl font-semibold">{{ $current_airport }}</span>
            <p class="text-base">@lang('airports.current')</p>
          </div>
        </div>
      </div>
    </div>
    <!-- PILOT STATS END -->

    <!-- CONTENT -->
    <div id="content" class="mb-7">
      <div class="flex flex-col md:flex-row">
        <div class="w-8/12">
          <div class="flex flex-col pb-2 md:pb-7 md:pr-8">
            <div class="bg-white rounded-md">
              <div class="py-3 px-6 rounded-t-md bg-blue-800 text-white">
                <span class="text-base font-semibold">Your last pilot report</span>
              </div>
              <div class="py-3 px-6">
                @if($last_pirep != null)
                  @include('dashboard.pirep_card', ['pirep' => $last_pirep])
                @else
                  <span>No pireps yet...</span>
                @endif
              </div>
            </div>
          </div>

          <div class="flex flex-col pb-2 md:pb-0 md:pr-8">
            <div class="bg-white rounded-md">
              <div class="py-3 px-6 rounded-t-md bg-blue-800 text-white">
                <span class="text-base font-semibold">News</span>
              </div>
              <div class="py-3 px-6">
                {{ Widget::latestNews(['count' => 1]) }}
              </div>
            </div>
          </div>
        </div>
        <div class="w-4/12">
          <div class="flex flex-col pb-2 md:pb-7">
            <div class="bg-white rounded-md">
              <div class="py-3 px-6 rounded-t-md bg-blue-800 text-white">
                <span class="text-base font-semibold">@lang('dashboard.weatherat', ['ICAO' => $current_airport])</span>
              </div>
              <div class="py-3 px-6">
                {{ Widget::Weather(['icao' => $current_airport]) }}
              </div>
            </div>
          </div>

          <div class="flex flex-col pb-2 md:pb-7">
            <div class="bg-white rounded-md">
              <div class="py-3 px-6 rounded-t-md bg-blue-800 text-white">
                <span class="text-base font-semibold">@lang('dashboard.recentreports')</span>
              </div>
              <div class="py-3 px-6">
                {{ Widget::latestPireps(['count' => 5]) }}
              </div>
            </div>
          </div>

          <div class="flex flex-col pb-2 md:pb-0">
            <div class="bg-white rounded-md">
              <div class="py-3 px-6 rounded-t-md bg-blue-800 text-white">
                <span class="text-base font-semibold">@lang('common.newestpilots')</span>
              </div>
              <div class="py-3 px-6">
                {{ Widget::latestPilots(['count' => 5]) }}
              </div>
            </div>
          </div>
        </div>
      </div>
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
