<div class="row">
  <div class="form-group col-sm-4">
    {{ Form::label('icao', 'ICAO (3LD):') }}&nbsp;<span class="required">*</span>
    {{ Form::text('icao', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('icao') }}</p>
  </div>
  <div class="form-group col-sm-4">
    {{ Form::label('iata', 'IATA (2LD):') }}
    {{ Form::text('iata', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('iata') }}</p>
  </div>
  <div class="form-group col-sm-4">
    {{ Form::label('callsign', 'Radio Callsign:') }}
    {{ Form::text('callsign', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('callsign') }}</p>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-4">
    {{ Form::label('name', 'Name:') }}&nbsp;<span class="required">*</span>
    {{ Form::text('name', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('name') }}</p>
  </div>
  <div class="form-group col-sm-8">
    {{ Form::label('logo', 'Logo URL:') }}
    {{ Form::text('logo', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('logo') }}</p>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-6">
    {{ Form::label('country', 'Country:') }} <br/>
    {{ Form::select('country', $countries, null, ['class' => 'form-control select2' ]) }}
    <p class="text-danger">{{ $errors->first('country') }}</p>
  </div>
  <div class="form-group col-sm-6">
    {{ Form::label('active', 'Active:') }}
    <br/>
    <label class="checkbox-inline">
      {{ Form::hidden('active', 0, false) }}
      {{ Form::checkbox('active', 1, null) }}
    </label>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-12">
    <div class="pull-right">
      {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
      <a href="{{ route('admin.airlines.index') }}" class="btn btn-default">Cancel</a>
    </div>
  </div>
</div>
