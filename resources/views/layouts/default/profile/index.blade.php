@extends('app')
@section('title', trans('frontend.global.profile'))

@section('content')
<div class="row profile-page content-center text-color-dark-beige">
    <div class="col-md-4" style="text-align:center;">
        <div class="photo-container">
            @if ($user->avatar == null)
                <img src="{{ $user->gravatar(512) }}" style="width: 123px;">
            @else
                <img src="{{ $user->avatar->url }}" style="width: 123px;">
            @endif
        </div>
        <h3 class="title">{{ $user->name }}</h3>
        <h6><span class="flag-icon flag-icon-{{ $user->country }}"></span></h6>
        <h6>{{ $user->pilot_id }}</h6>
        <h6>{{ $user->rank->name }}</h6>
        <p class="description" style="color: #9A9A9A;">
            {{ $user->airline->name }}
        </p>
    </div>
    <div class="col-md-8  content-center">
        <div class="content">
            <div class="social-description">
                <h2>{{ $user->flights}}</h2>
                <p>{{ trans_choice('common.flight', $user->flights) }}</p>
            </div>

            <div class="social-description">
                <h2>{{ \App\Facades\Utils::minutesToTimeString($user->flight_time, false) }}</h2>
                <p>@lang('frontend.profile.flighthours')</p>
            </div>

            @if($user->home_airport)
                <div class="social-description">
                    <h2>{{ $user->home_airport->icao }}</h2>
                    <p>@lang('common.homeairport')</p>
                </div>
            @endif

            @if($user->current_airport)
                <div class="social-description">
                    <h2>{{ $user->current_airport->icao }}</h2>
                    <p>@lang('common.currentairport')</p>
                </div>
            @endif

        </div>
    </div>
</div>

{{--
    show the details/edit fields only for the currently logged in user
--}}
@if(Auth::check() && $user->id === Auth::user()->id)
    <div class="clearfix" style="height: 50px;"></div>
    <div class="row">
        <div class="col-sm-12">
            <div class="text-right">
                <a href="{{ route('frontend.profile.regen_apikey') }}" class="btn btn-warning"
                   onclick="return confirm({{ __('Are you sure? This will reset your API key.') }})">@lang('frontend.profile.newapikey')</a>
                &nbsp;
                <a href="{{ route('frontend.profile.edit', ['id' => $user->id]) }}"
                   class="btn btn-primary">@lang('common.edit')</a>
            </div>

            <h3 class="description">@lang('frontend.profile.yourprofile')</h3>
            <table class="table table-full-width">
                <tr>
                    <td>@lang('common.email')</td>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <td>@lang('frontend.profile.apikey')&nbsp;&nbsp;<span class="description">(@lang('frontend.profile.dontshare'))</span></td>
                    <td>{{ $user->api_key }}</td>
                </tr>
                <tr>
                    <td>@lang('common.timezone')</td>
                    <td>{{ $user->timezone }}</td>
                </tr>
            </table>
        </div>
    </div>
@endif
@endsection
