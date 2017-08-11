@extends('layouts.default.app')

@section('content')
<div class="row">
<div class="col-sm-4"></div>
<div class="col-sm-4">
<form class="form-signin" role="form" method="POST" action="{{ url('/register') }}">
    {{ csrf_field() }}

    <div class="panel periodic-login">
        <div class="panel-body text-center">
            <h4>Register</h4>
            <label for="name" class="col-md-4 control-label">Full Name</label>
            <div class="input-group form-group-no-border{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Full Name" required>
            </div>
            @if ($errors->has('name'))
            <p class="text-danger">{{ $errors->first('name') }}</p>
            @endif

            <label for="email" class="col-md-4 control-label">Email Address</label>
            <div class="input-group form-group-no-border{{ $errors->has('email') ? ' has-danger' : '' }}">
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email Address" required>
            </div>
            @if ($errors->has('email'))
            <p class="text-danger">{{ $errors->first('email') }}</p>
            @endif

            <label for="airline" class="col-md-4 control-label">Airline</label>
            <div class="input-group form-group-no-border{{ $errors->has('airline') ? ' has-danger' : '' }}">
                    <select name="airline" id="airline" class="form-control" required>
                        @foreach($airlines as $airline)
                        <option value="{{ $airline->id }}">{{ $airline->code }} - {{ $airline->name }}</option>
                        @endforeach
                    </select>
            </div>
            @if ($errors->has('airline'))
            <p class="text-danger">{{ $errors->first('airline') }}</p>
            @endif

            <label for="home_airport" class="col-md-4 control-label">Home Airport</label>
            <div class="input-group form-group-no-border{{ $errors->has('home_airport') ? ' has-danger' : '' }}">
                    <select name="home_airport" id="home_airport" class="form-control" required>
                        @foreach($airports as $airport)
                        <option value="{{ $airport->id }}">{{ $airport->icao }} - {{ $airport->name }}</option>
                        @endforeach
                    </select>
            </div>
            @if ($errors->has('home_airport'))
            <p class="text-danger">{{ $errors->first('home_airport') }}</p>
            @endif

            <label for="password" class="col-md-4 control-label">Password</label>
            <div class="input-group form-group-no-border{{ $errors->has('password') ? ' has-danger' : '' }}">
                    <input id="password" type="password" class="form-control" name="password" value="" placeholder="Password" required>
            </div>
            @if ($errors->has('password'))
            <p class="text-danger">{{ $errors->first('password') }}</p>
            @endif

            <label for="password_confirmation" class="col-md-4 control-label">Confirm Password</label>
            <div class="input-group form-group-no-border{{ $errors->has('password_confirmation') ? ' has-danger' : '' }}">
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" value="" placeholder="Confirm Password" required>
            </div>
            @if ($errors->has('password_confirmation'))
            <p class="text-danger">{{ $errors->first('password_confirmation') }}</p>
            @endif
            <button type="submit" class="btn btn-primary">Register</button>

        </div>
    </div>

</form>
</div>
<div class="col-sm-4"></div>
</div>
@endsection
