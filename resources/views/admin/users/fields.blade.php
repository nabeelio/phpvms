<div class="row">
  <div class="form-group col-sm-1">
    {{ Form::label('id', 'ID:') }}
    {{ Form::number('id', null, ['class' => 'form-control', 'readonly' => 'readonly']) }}
  </div>
  <div class="form-group col-sm-3">
    {{ Form::label('name', 'Name:') }}
    {{ Form::text('name', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
    <p class="text-danger">{{ $errors->first('name') }}</p>
  </div>
  <div class="form-group col-sm-2">
    {{ Form::label('email', 'Email:') }}
    {{ Form::text('email', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
    <p class="text-danger">{{ $errors->first('email') }}</p>
  </div>
  <div class="form-group col-sm-2">
    {{ Form::label('password', 'Password:') }}
    {{ Form::password('password', ['class' => 'form-control', 'autocomplete' => 'off']) }}
    <p class="text-danger">{{ $errors->first('password') }}</p>
  </div>
  <div class="form-group col-sm-2">
    {{ Form::label('country', 'Country:') }} <br/>
    {{ Form::select('country', $countries, null, ['class' => 'form-control select2' ]) }}
    <p class="text-danger">{{ $errors->first('country') }}</p>
  </div>
  <div class="form-group col-sm-2">
    {{ Form::label('timezone', 'Timezone:') }} <br/>
    {{ Form::select('timezone', $timezones, null, ['id' => 'timezone', 'class' => 'form-control select2' ]) }}
    <p class="text-danger">{{ $errors->first('timezone') }}</p>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-1">
    {{ Form::label('pilot_id', 'Ident:') }}
    {{ Form::number('pilot_id', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('pilot_id') }}</p>
  </div>
  <div class="form-group col-sm-1">
    {{ Form::label('callsign', 'Callsign:') }}
    {{ Form::text('callsign', null, ['class' => 'form-control', 'autocomplete' => 'off', 'maxlength' => 4]) }}
    <p class="text-danger">{{ $errors->first('callsign') }}</p>
  </div>
  <div class="form-group col-sm-1">
    {{ Form::label('transfer_time', 'Transfer Hours:') }}
    {{ Form::text('transfer_time', \App\Support\Units\Time::minutesToHours($user?->transfer_time), ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('transfer_time') }}</p>
  </div>
  <div class="form-group col-sm-3">
    {{ Form::label('airline_id', 'Airline:') }}
    {{ Form::select('airline_id', $airlines, null, ['class' => 'form-control select2', 'placeholder' => 'Select Airline']) }}
  </div>
  <div class="form-group col-sm-3">
    {{ Form::label('rank_id', 'Rank:') }}
    {{ Form::select('rank_id', $ranks, null, ['class' => 'form-control select2', 'placeholder' => 'Select Rank']) }}
  </div>
  <div class="form-group col-md-3">
    {{ Form::label('state', 'State:') }}
    {{ Form::select('state', UserState::labels(), null, ['class' => 'form-control select2', 'style' => 'width: 100%;']) }}
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-3">
    {{ Form::label('home_airport_id', 'Home Airport:') }}
    {{ Form::select('home_airport_id', $airports, null , ['class' => 'form-control airport_search']) }}
    <p class="text-danger">{{ $errors->first('home_airport_id') }}</p>
  </div>
  <div class="form-group col-sm-3">
    {{ Form::label('curr_airport_id', 'Current Airport:') }}
    {{ Form::select('curr_airport_id', $airports, null , ['class' => 'form-control airport_search']) }}
    <p class="text-danger">{{ $errors->first('curr_airport_id') }}</p>
  </div>
  <div class="form-group col-sm-6">
    @ability('admin', 'admin-user')
      {{ Form::label('roles', 'Roles:') }}
      {{ Form::select('roles[]', $roles, $user?->roles->pluck('id') ?? collect(), ['class' => 'form-control select2', 'placeholder' => 'Select Roles', 'multiple']) }}
    @endability
  </div>
</div>

<div class="row">
  <div class="form-group col-md-12">
    {{ Form::label('notes', 'Management Notes:') }}
    {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 4, 'autocomplete' => 'off']) }}
  </div>
</div>
