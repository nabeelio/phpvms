@extends('auth.layout')

@section('content')

    <form class="form-signin" method="POST" action="{{ url('/login') }}">
        {{ csrf_field() }}
        <div class="panel periodic-login">
            <div class="panel-body text-center">
                {{--<h1 class="atomic-symbol">Mi</h1>--}}
                {{--<p class="atomic-mass">14.072110</p>
                <p class="element-name">Miminium</p>--}}
                <p><img src="assets/frontend/img/logo_login.png" /></p>

                {{--<i class="icons icon-arrow-down"></i>--}}
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} form-animate-text" style="margin-top:40px !important;">

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
                    <label for="password" class="col-md-4 control-label">password</label>
                </div>
                <label class="pull-left">
                    <input type="checkbox" name="remember"> Remember Me
                </label>
                <button type="submit" class="btn btn col-md-12">
                    Login
                </button>
            </div>
            <div class="text-center" style="padding:5px;">
                <a href="{{ url('/password/reset') }}">Forgot Password</a>
                <a href="{{ url('/register') }}">| Signup</a>
            </div>
        </div>
    </form>
@endsection
