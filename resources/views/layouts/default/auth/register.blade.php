@extends('layouts.default.app')
@section('title', 'register')
@section('content')
<div class="row">
<div class="col-sm-3"></div>
<div class="col-sm-6">

{!! Form::open(['url' => '/register', 'class' => 'form-signin']) !!}

    <div class="panel periodic-login">
        <div class="panel-body">
            <h2>Register</h2>
            <label for="name" class="control-label">Full Name</label>
            <div class="input-group form-group-no-border{{ $errors->has('name') ? ' has-danger' : '' }}">
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>
            @if ($errors->has('name'))
            <p class="text-danger">{{ $errors->first('name') }}</p>
            @endif

            <label for="email" class="control-label">Email Address</label>
            <div class="input-group form-group-no-border{{ $errors->has('email') ? ' has-danger' : '' }}">
                {!! Form::text('email', null, ['class' => 'form-control']) !!}
            </div>
            @if ($errors->has('email'))
                <p class="text-danger">{{ $errors->first('email') }}</p>
            @endif

            <label for="airline" class="control-label">Airline</label>
            <div class="input-group form-group-no-border{{ $errors->has('airline') ? ' has-danger' : '' }}">
                {!! Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) !!}
            </div>
            @if ($errors->has('airline_id'))
            <p class="text-danger">{{ $errors->first('airline_id') }}</p>
            @endif

            <label for="home_airport" class="control-label">Home Airport</label>
            <div class="input-group form-group-no-border{{ $errors->has('home_airport') ? ' has-danger' : '' }}">
                {!! Form::select('home_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
            </div>
            @if ($errors->has('home_airport_id'))
            <p class="text-danger">{{ $errors->first('home_airport_id') }}</p>
            @endif

            <label for="timezone" class="control-label">Timezone</label>
            <div class="input-group form-group-no-border{{ $errors->has('timezone') ? ' has-danger' : '' }}">
                {!! Form::select('timezone', $timezones, null, ['id'=>'timezone', 'class' => 'form-control select2' ]); !!}
            </div>
            @if ($errors->has('timezone'))
                <p class="text-danger">{{ $errors->first('timezone') }}</p>
            @endif

            <label for="password" class="control-label">Password</label>
            <div class="input-group form-group-no-border{{ $errors->has('password') ? ' has-danger' : '' }}">
                {!! Form::password('password', ['class' => 'form-control']) !!}
            </div>
            @if ($errors->has('password'))
            <p class="text-danger">{{ $errors->first('password') }}</p>
            @endif

            <label for="password_confirmation" class="control-label">Confirm Password</label>
            <div class="input-group form-group-no-border{{ $errors->has('password_confirmation') ? ' has-danger' : '' }}">
                {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
            </div>
            @if ($errors->has('password_confirmation'))
            <p class="text-danger">{{ $errors->first('password_confirmation') }}</p>
            @endif

            @include('layouts.default.auth.toc')

            <div style="width: 100%; text-align: right; padding-top: 20px;">
                By registering, you agree to the Term and Conditions<br /><br />
                {!! Form::submit('Register!', ['class' => 'btn btn-primary']) !!}
            </div>

        </div>
    </div>

</form>
</div>
<div class="col-sm-4"></div>
</div>
@endsection
