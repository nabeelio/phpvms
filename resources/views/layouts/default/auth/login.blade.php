@extends('auth.layout')
@section('title', __('common.login'))

@section('content')
<div class="navbar-translate">
    <a class="navbar-brand" href="https://demos.creative-tim.com/now-ui-kit/index.html" rel="tooltip"
       title="Designed by Invision. Coded by Creative Tim" data-placement="bottom" target="_blank">
        Now Ui Kit
    </a>
    <button class="navbar-toggler navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation"
            aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-bar top-bar"></span>
        <span class="navbar-toggler-bar middle-bar"></span>
        <span class="navbar-toggler-bar bottom-bar"></span>
    </button>
</div>
<div class="col-md-4 ml-auto mr-auto content-center">
    <div class="card card-login card-plain">
        {{ Form::open(['url' => url('/login'), 'method' => 'post']) }}
            <div class="header header-primary text-center">
                <div class="logo-container" style="width: 320px;">
                    <img src="{{ public_asset('/assets/frontend/img/logo.svg') }}" width="320" height="320" style="background: #FFF">
                </div>
            </div>
            <div class="content">
                <div class="input-group form-group-no-border{{ $errors->has('email') ? ' has-error' : '' }} input-lg">
                    <span class="input-group-addon">
                        <i class="now-ui-icons users_circle-08"></i>
                    </span>
                    {{
                        Form::text('email', old('email'), [
                            'id' => 'email',
                            'placeholder' => __('common.email'),
                            'class' => 'form-control',
                            'required' => true,
                        ])
                    }}
                </div>
                @if ($errors->has('email'))
                    <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif

                <div class="input-group form-group-no-border{{ $errors->has('password') ? ' has-error' : '' }} input-lg">
                    <span class="input-group-addon">
                        <i class="now-ui-icons ui-1_lock-circle-open"></i>
                    </span>
                    {{
                        Form::password('password', [
                            'name' => 'password',
                            'class' => 'form-control',
                            'placeholder' => __('auth.password'),
                            'required' => true,
                        ])
                    }}
                </div>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif

            </div>
            <div class="footer text-center">
                <button class="btn btn-primary btn-round btn-lg btn-block">@lang('common.login')</button>
            </div>
            <div class="pull-left">
                <h6>
                    <a href="{{ url('/register') }}" class="link">@lang('auth.createaccount')</a>
                </h6>
            </div>
            <div class="pull-right">
                <h6>
                    <a href="{{ url('/password/reset') }}" class="link">@lang('auth.forgotpassword')?</a>
                </h6>
            </div>
        {{ Form::close() }}
    </div>
</div>
@endsection
