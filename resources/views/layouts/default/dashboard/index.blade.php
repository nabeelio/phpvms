@extends('app')
@section('title', 'dashboard')

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
                        <h5 class="description">{{ str_plural('flight', $user->flights) }}</h5>
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
                        <h5 class="description">total hours</h5>
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
                        <h5 class="description">your balance</h5>
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
                        <h5 class="description">current airport</h5>
                    </div>
                </div>
            </div>

        </div>

        @if($last_pirep === null)
        <div class="card">
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
                Your Last Report
            </div>
            <div class="card-block" style="text-align:center;">
                    No reports yet. <a href="{{ route('frontend.pireps.create') }}">File one now.</a>
            </div>
        </div>
        @else
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
                Your Last Report
            </div>
            @include('pireps.pirep_card', ['pirep' => $last_pirep])
        @endif

        {{ Widget::latestNews(['count' => 1]) }}

    </div>

    {{-- Sidebar --}}
    <div class="col-sm-4">
        <div class="card">
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
                Weather at {{ $current_airport }}
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
                Recent Reports
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
                Newest Pilots
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
