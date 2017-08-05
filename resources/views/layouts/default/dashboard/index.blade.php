@extends('layouts.default.app')

@section('content')
<!--
<div class="row">
    <div class="col-sm-2">
        <div class="card card-primary text-white">
            <div class="card-block text-center">
                <h4 class="">{!! $user->flights !!}</h4>
                <h5 class="description" style="color: white;">flights</h5>
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="card card-primary text-white">
            <div class="card-block text-center">
                <h4 class="">{!! \App\Facades\Utils::secondsToTime($user->flight_time, false)!!}</h4>
                <h5 class="description" style="color: white;">hours</h5>
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="card card-primary text-white">
            <div class="card-block text-center">
                <h4 class="">{!! $user->current_airport->icao !!}</h4>
                <h5 class="description" style="color: white;">current airport</h5>
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="card card-primary text-white">
            <div class="card-block text-center">
                <h4 class="">{!! $user->current_airport->icao !!}</h4>
                <h5 class="description" style="color: white;">current airport</h5>
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="card card-primary text-white">
            <div class="card-block text-center">
                <h4 class="">{!! $user->current_airport->icao !!}</h4>
                <h5 class="description" style="color: white;">current airport</h5>
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="card card-primary text-white">
            <div class="card-block text-center">
                <h4 class="">{!! $user->current_airport->icao !!}</h4>
                <h5 class="description" style="color: white;">current airport</h5>
            </div>
        </div>
    </div>
</div>
-->
{{--<h3 class="description">welcome back, {!! $user->name !!}</h3>--}}
<div class="row">
    <div class="col-sm-8">
        <div class="row">
            <div class="col-sm-4">
                <div class="card card-primary text-white" style="background: #067ec1; color: #FFF;">
                    <div class="card-block text-center">
                        <h4 class="">{!! $user->flights !!}</h4>
                        <h5 class="description" style="color: white;">{{ str_plural('flight', $user->flights) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card card-primary text-white" style="background: #067ec1; color: #FFF;">
                    <div class="card-block text-center">
                        <h4 class="">{!! \App\Facades\Utils::secondsToTime($user->flight_time, false)!!}</h4>
                        <h5 class="description" style="color: white;">total hours</h5>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card card-primary text-white" style="background: #067ec1; color: #FFF;">
                    <div class="card-block text-center">
                        <h4 class="">{!! $user->current_airport->icao !!}</h4>
                        <h5 class="description" style="color: white;">current airport</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
                Your Last Report
            </div>
            <div class="card-block">
                <!-- Tab panes -->
                <div class="tab-content">
                    
                </div>
            </div>
        </div>

        <div class="card">
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
                News
            </div>
            <div class="card-block">
                <!-- Tab panes -->
                <div class="tab-content">
                    News goes here!
                </div>
            </div>
        </div>

    </div>
    <div class="col-sm-4">

        <div class="card">
            <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
                Recent Reports
            </div>
            <div class="card-block">
                <!-- Tab panes -->
                <div class="tab-content">
                    <table>
                    @foreach($pireps as $p)
                    <tr>
                        <td style="padding-right: 10px;">{!! $p->airline->code !!}{!! $p->flight->flight_number !!}</td>
                        <td>
                            <span class="description">{!! $p->dpt_airport->icao !!}</span>-
                            <span class="description">{!! $p->arr_airport->icao !!}</span>&nbsp;
                            <span class="description">{!! $p->aircraft->name !!}</span>
                        </td>
                    </tr>
                    @endforeach
                    </table>
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
                    <table>
                        @foreach($users as $u)
                        <tr>
                            <td style="padding-right: 10px;">{!! $u->pilot_id() !!}</td>
                            <td><span class="description">{!! $u->name !!}</span></td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
