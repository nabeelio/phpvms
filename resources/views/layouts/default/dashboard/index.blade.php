@extends('layouts.default.app')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary text-white">
            <div class="card-block text-center">
                <h1 class="">{!! $user->flights !!}</h1>
                <h2 class="description" style="color: white;">flights</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-primary text-white">
            <div class="card-block text-center">
                <h1 class="">{!! \App\Facades\Utils::secondsToTime($user->flight_time, false)!!}</h1>
                <h2 class="description" style="color: white;">hours</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-primary text-white">
            <div class="card-block text-center">
                <h1 class="">{!! $user->current_airport->icao !!}</h1>
                <h2 class="description" style="color: white;">current airport</h2>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="separator separator-info"></div>
</div>
<div class="row">
    <div class="col-sm-10">
        <div class="card">
            <div class="card-block">
                <p class="category">News</p>
                <!-- Tab panes -->
                <div class="tab-content">
                    News goes here!
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2 text-center">
        <p><a href="#" class="btn btn-info btn-round">
                <i class="now-ui-icons ui-2_favourite-28"></i> New Report
            </a>
        </p>

        <p><a href="#" class="btn btn-info btn-round">
                <i class="now-ui-icons ui-2_favourite-28"></i> My Reports
            </a></p>
    </div>
</div>
@endsection
