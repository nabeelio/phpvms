@extends('app')
@section('title', __('Dashboard'))

@section('content')
<div class="row">
    <div class="col-sm-8">

        {{-- TOP BAR WITH BOXES --}}
        <div class="row">
            <div class="col-sm-3">
                <div class="card card-primary text-white dashboard-box">
                    <div class="card-block text-center">
                        <div class="icon-background">
                            <i class="fas fa-plane icon"></i>
                        </div>
                        <h3 class="header">{{ $user->flights }}</h3>
                        <h5 class="description">{{ __trans_choice('Flight', $user->flights) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card card-primary text-white dashboard-box">
                    <div class="card-block text-center">
                        <div class="icon-background">
                            <i class="far fa-clock icon"></i>
                        </div>
                        <h3 class="header">{{ \App\Facades\Utils::minutesToTimeString($user->flight_time, false)}}</h3>
                        <h5 class="description">{{ __('Total Hours') }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card card-primary text-white dashboard-box">
                    <div class="card-block text-center">
                        <div class="icon-background"> {{--110px font-size--}}
                            <i class="fas fa-money-bill-alt icon"></i>
                        </div>
                        <h3 class="header">{{ $user->journal->balance }}</h3>
                        <h5 class="description">{{ __('Your Balance') }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card card-primary text-white dashboard-box">
                    <div class="card-block text-center">
                        <div class="icon-background">
                            <i class="fas fa-map-marker icon"></i>
                        </div>
                        @if($user->current_airport)
                            <h3 class="header">{{ $user->curr_airport_id }}</h3>
                        @else
                            <h3 class="header">{{ $user->home_airport_id }}</h3>
                        @endif
                        <h5 class="description">{{ __('Current Airport') }}</h5>
                    </div>
                </div>
            </div>

        </div>

        <div class="card">
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
			{{ __('Your Last Report') }}
            </div>
        @if($last_pirep === null)
            <div class="card-block" style="text-align:center;">
				{{ __('No reports yet.') }} <a href="{{ route('frontend.pireps.create') }}">{{ __('File one now.') }}</a>
            </div>
        @else
            @include('pireps.pirep_card', ['pirep' => $last_pirep])
        @endif
        </div>

        {{ Widget::latestNews(['count' => 1]) }}

    </div>

    {{-- Sidebar --}}
    <div class="col-sm-4">
        <div class="card">
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
				{{ __('Weather at :ICAO', ['ICAO' => $current_airport]) }}
            </div>
            <div class="card-block">
                <!-- Tab panes -->
                <div class="tab-content">
                    {{ Widget::Weather(['icao' => $current_airport]) }}
                </div>
            </div>
        </div>

        <div class="card">
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
				{{ __('Recent Reports') }}
            </div>
            <div class="card-block">
                <!-- Tab panes -->
                <div class="tab-content">
                    {{ Widget::latestPireps(['count' => 5]) }}
                </div>
            </div>
        </div>

        <div class="card">
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
				{{ __('Newest Pilots') }}
            </div>
            <div class="card-block">
                <!-- Tab panes -->
                <div class="tab-content">
                    {{ Widget::latestPilots(['count' => 5]) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
