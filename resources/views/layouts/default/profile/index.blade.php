@extends('layouts.default.app')
@section('title', 'profile')
@section('content')
<div class="container profile-page">
        <div class="page-header page-header-small text-color-dark-beige">
            <div class="page-header-image"></div>
            <div class="container text-color-dark-beige">
                <div class="content-center" style="color: #9b9992;">
                    <div class="photo-container">
                        <img src="/assets/frontend/img/logo.svg" alt="">
                    </div>
                    <h3 class="title">{!! $user->name !!}</h3>
                    <h6>{!! $user->rank->name !!}</h6>
                    <p class="description" style="color: #9A9A9A;">
                        {!! $user->airline->name !!}
                    </p
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
</div>
@endsection
