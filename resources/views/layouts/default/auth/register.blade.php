@extends('layouts.default.auth.layout')

@section('content')

<form class="form-signin" role="form" method="POST" action="{{ url('/register') }}">
    {{ csrf_field() }}

    <div class="panel periodic-login">
        <div class="panel-body text-center">
            {{--<h1 class="atomic-symbol">Mi</h1>--}}
            {{--<p class="atomic-mass">14.072110</p>
            <p class="element-name">Miminium</p>--}}
            <p><img src="assets/frontend/img/logo_login.png" /></p>

            {{--<i class="icons icon-arrow-down"></i>--}}
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} form-animate-text" style="margin-top:40px !important;">

                <input id="name" type="text" class="form-text" name="name" value="{{ old('name') }}" required autofocus>

                @if ($errors->has('name'))
                    <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
                @endif
                <span class="bar"></span>
                <label for="email" class="col-md-4 control-label">name</label>
            </div>

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} form-animate-text"
                 style="margin-top:40px !important;">
                <input id="email" type="email" class="form-text" name="email" value="{{ old('email') }}" required autofocus>
                @if ($errors->has('email'))
                    <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
                <span class="bar"></span>
                <label for="email" class="col-md-4 control-label">email</label>
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} form-animate-text" style="margin-top:40px !important;">
                <input id="password" type="password" class="form-text" name="password" required>
                @if ($errors->has('password'))
                    <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
                <span class="bar"></span>
                <label for="email" class="col-md-4 control-label">password</label>
            </div>

            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }} form-animate-text" style="margin-top:40px !important;">
                <input id="password-confirm" type="password" class="form-text" name="password_confirmation" required>

                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
                @endif

                <span class="bar"></span>
                <label for="email" class="col-md-4 control-label">confirm password</label>
            </div>

            <div class="text-center">
                <a href="/login" class="btn btn-primary">Login</a>
                <button type="submit" class="btn btn-primary">
                    Register
                </button>
            </div>

        </div>
    </div>

</form>
@endsection
