@extends('layouts.default.app')
@section('title', 'profile')
@section('content')
<div class="row profile-page content-center text-color-dark-beige">
    <div class="col-md-4" style="text-align:center;">
        <div class="photo-container">
            <img src="{!! public_asset('/assets/frontend/img/logo.svg') !!}" alt="">
        </div>
        <h3 class="title">{!! $user->name !!}</h3>
        <h6>{!! $user->pilot_id !!}</h6>
        <h6>{!! $user->rank->name !!}</h6>
        <p class="description" style="color: #9A9A9A;">
            {!! $user->airline->name !!}
        </p>
    </div>
    <div class="col-md-8  content-center">
        <div class="content">
            <div class="social-description">
                <h2>{!! $user->flights!!}</h2>
                <p>Flights</p>
            </div>

            <div class="social-description">
                <h2>{!! \App\Facades\Utils::secondsToTimeString($user->flight_time, false)!!}</h2>
                <p>Flight Hours</p>
            </div>

            @if($user->home_airport)
                <div class="social-description">
                    <h2>{!! $user->home_airport->icao !!}</h2>
                    <p>Home Airport</p>
                </div>
            @endif

            @if($user->current_airport)
                <div class="social-description">
                    <h2>{!! $user->current_airport->icao !!}</h2>
                    <p>Current Airport</p>
                </div>
            @endif

        </div>
    </div>
</div>

@if(Auth::check() && $user->id === Auth::user()->id)
    <div class="clearfix" style="height: 50px;"></div>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-sm-10">
            <a href="{!! route('frontend.profile.edit', ['id'=>$user->id]) !!}" class="pull-right btn btn-primary">edit</a>
            <h3 class="description">your profile</h3>
            <table class="table table-full-width">
                <tr>
                    <td>Email</td>
                    <td>{!! $user->email !!}</td>
                </tr>
                <tr>
                    <td>API Key<p class="description">don't share this!</p></td>
                    <td>{!! $user->api_key !!}</td>
                </tr>
                <tr>
                    <td>Timezone</td>
                    <td>{!! $user->timezone !!}</td>
                </tr>
            </table>
        </div>
    </div>
@endif
{{--<div class="container profile-page">
    <div class="page-header page-header-small text-color-dark-beige">
        <div class="container text-color-dark-beige">
            <div class="content-center" style="color: #9b9992;">
                <div class="photo-container">
                    <img src="{!! public_asset('/assets/frontend/img/logo.svg') !!}" alt="">
                </div>
                <h3 class="title">{!! $user->name !!}</h3>
                <h6>{!! $user->rank->name !!}</h6>
                <p class="description" style="color: #9A9A9A;">
                    {!! $user->airline->name !!}
                </p>
                <br /><br />
                <div class="content" style="max-width: 650px;">

                    <div class="social-description">
                        <h2>{!! $user->flights!!}</h2>
                        <p>Flights</p>
                    </div>

                    <div class="social-description">
                        <h2>{!! \App\Facades\Utils::secondsToTimeString($user->flight_time, false)!!}</h2>
                        <p>Flight Hours</p>
                    </div>

                    @if($user->home_airport)
                    <div class="social-description">
                        <h2>{!! $user->home_airport->icao !!}</h2>
                        <p>Home Airport</p>
                    </div>
                    @endif

                    @if($user->current_airport)
                        <div class="social-description">
                            <h2>{!! $user->current_airport->icao !!}</h2>
                            <p>Current Airport</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>--}}

@endsection
