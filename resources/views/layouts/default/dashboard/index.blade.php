@extends("layouts.${SKIN_NAME}.app")
@section('title', 'dashboard')

@section('content')
<div class="row">
    <div class="col-sm-8">
        <div class="row">
            <div class="col-sm-4">
                <div class="card card-primary text-white" style="background: #067ec1; color: #FFF;">
                    <div class="card-block text-center">
                        <div style="float: left; position: absolute; display:block; top: 0px;font-size: 150px">
                            <i class="fas fa-plane" style="opacity: .1;"></i>
                        </div>
                        <h4 class="">{!! $user->flights !!}</h4>
                        <h5 class="description" style="color: white;">{{ str_plural('flight', $user->flights) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card card-primary text-white" style="background: #067ec1; color: #FFF;">
                    <div class="card-block text-center">
                        <div style="float: left; position: absolute; display:block; top: 0px;font-size: 150px">
                            <i class="far fa-clock" style="opacity: .1;"></i>
                        </div>
                        <h4 class="">{!! \App\Facades\Utils::minutesToTimeString($user->flight_time, false)!!}</h4>
                        <h5 class="description" style="color: white;">total hours</h5>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card card-primary text-white" style="background: #067ec1; color: #FFF;">
                    <div class="card-block text-center">
                        <div style="float: left; position: absolute; display:block; top: 0px;font-size: 150px">
                            <i class="fas fa-map-marker" style="opacity: .1;"></i>
                        </div>
                        @if($user->current_airport)
                            <h4 class="">{!! $user->current_airport->icao !!}</h4>
                        @else
                            <h4 class="">-</h4>
                        @endif
                        <h5 class="description" style="color: white;">current airport</h5>
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
                    No reports yet. <a href="{!! route('frontend.pireps.create') !!}">File one now.</a>
            </div>
        </div>
        @else
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
                Your Last Report
            </div>
            @include("layouts.${SKIN_NAME}.pireps.pirep_card", ['pirep' => $last_pirep])
        @endif

        {{ Widget::latestNews(['count' => 1]) }}

    </div>
    <div class="col-sm-4">
        {{ Widget::latestPireps(['count' => 5]) }}
        {{ Widget::latestPilots(['count' => 5]) }}
    </div>
</div>
@endsection
