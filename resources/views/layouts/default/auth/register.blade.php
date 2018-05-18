@extends('app')
@section('title', trans('frontend.global.register'))

@section('content')
<div class="row">
<div class="col-sm-3"></div>
<div class="col-sm-6">

{{ Form::open(['url' => '/register', 'class' => 'form-signin']) }}

    <div class="panel periodic-login">
        <div class="panel-body">
            <h2>@lang('frontend.global.register')</h2>
            <label for="name" class="control-label">@lang('frontend.auth.fullname')</label>
            <div class="input-group form-group-no-border {{ $errors->has('name') ? 'has-danger' : '' }}">
                {{ Form::text('name', null, ['class' => 'form-control']) }}
            </div>
            @if ($errors->has('name'))
            <p class="text-danger">{{ $errors->first('name') }}</p>
            @endif

            <label for="email" class="control-label">@lang('frontend.auth.emailaddress')</label>
            <div class="input-group form-group-no-border {{ $errors->has('email') ? 'has-danger' : '' }}">
                {{ Form::text('email', null, ['class' => 'form-control']) }}
            </div>
            @if ($errors->has('email'))
                <p class="text-danger">{{ $errors->first('email') }}</p>
            @endif

            <label for="airline" class="control-label">@lang('frontend.global.airline')</label>
            <div class="input-group form-group-no-border {{ $errors->has('airline') ? 'has-danger' : '' }}">
                {{ Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) }}
            </div>
            @if ($errors->has('airline_id'))
            <p class="text-danger">{{ $errors->first('airline_id') }}</p>
            @endif

            <label for="home_airport" class="control-label">@lang('frontend.global.homeairport')</label>
            <div class="input-group form-group-no-border {{ $errors->has('home_airport') ? 'has-danger' : '' }}">
                {{ Form::select('home_airport_id', $airports, null , ['class' => 'form-control select2']) }}
            </div>
            @if ($errors->has('home_airport_id'))
            <p class="text-danger">{{ $errors->first('home_airport_id') }}</p>
            @endif

            <label for="country" class="control-label">@lang('frontend.global.country')</label>
            <div class="input-group form-group-no-border {{ $errors->has('country') ? 'has-danger' : '' }}">
                {{ Form::select('country', $countries, null, ['class' => 'form-control select2' ]) }}
            </div>
            @if ($errors->has('country'))
                <p class="text-danger">{{ $errors->first('country') }}</p>
            @endif

            <label for="timezone" class="control-label">@lang('frontend.global.timezone')</label>
            <div class="input-group form-group-no-border {{ $errors->has('timezone') ? 'has-danger' : '' }}">
                {{ Form::select('timezone', $timezones, null, ['id'=>'timezone', 'class' => 'form-control select2' ]) }}
            </div>
            @if ($errors->has('timezone'))
                <p class="text-danger">{{ $errors->first('timezone') }}</p>
            @endif

            <label for="password" class="control-label">@lang('frontend.auth.password')</label>
            <div class="input-group form-group-no-border {{ $errors->has('password') ? 'has-danger' : '' }}">
                {{ Form::password('password', ['class' => 'form-control']) }}
            </div>
            @if ($errors->has('password'))
            <p class="text-danger">{{ $errors->first('password') }}</p>
            @endif

            <label for="password_confirmation" class="control-label">@lang('frontend.global.confirmpassword')</label>
            <div class="input-group form-group-no-border {{ $errors->has('password_confirmation') ? 'has-danger' : '' }}">
                {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
            </div>
            @if ($errors->has('password_confirmation'))
                <p class="text-danger">{{ $errors->first('password_confirmation') }}</p>
            @endif

            @if(config('captcha.enabled'))
                <label for="g-recaptcha-response" class="control-label">@lang('frontend.auth.fillcaptcha')</label>
                <div class="input-group form-group-no-border {{ $errors->has('g-recaptcha-response') ? 'has-danger' : '' }}">
                    {!! NoCaptcha::display(config('captcha.attributes')) !!}
                </div>
                @if ($errors->has('g-recaptcha-response'))
                    <p class="text-danger">{{ $errors->first('g-recaptcha-response') }}</p>
                @endif
            @endif

            @include('auth.toc')

            <div style="width: 100%; text-align: right; padding-top: 20px;">
                @lang('frontend.auth.tocaccept')<br /><br />
                {{ Form::submit(trans('frontend.auth.register'), ['class' => 'btn btn-primary']) }}
            </div>

        </div>
    </div>

</form>
</div>
<div class="col-sm-4"></div>
</div>
@endsection

@section('scripts')
{!! NoCaptcha::renderJs(config('app.locale')) !!}
@endsection
