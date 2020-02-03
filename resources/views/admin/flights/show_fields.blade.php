<div class="form-group col-sm-6">
  <div class="box box-solid">
    <div class="box-header with-border">
      {{--<i class="fa fa-text-width"></i>--}}
      <h3 class="box-title">{{ Form::label('dpt_airport_id', 'Dep ICAO') }}</h3>
    </div>
    <div class="box-body"><p class="lead">
        {{ $flight->dpt_airport_id }} - {{ optional($flight->dpt_airport)->name }}
      </p></div>
  </div>
</div>

<div class="form-group col-sm-6">
  <div class="box box-solid">
    <div class="box-header with-border">
      {{--<i class="fa fa-text-width"></i>--}}
      <h3 class="box-title">{{ Form::label('arr_airport_id', 'Arrival ICAO') }}</h3>
    </div>
    <div class="box-body"><p class="lead">
        {{ $flight->arr_airport_id }} - {{ optional($flight->arr_airport)->name }}
      </p>
    </div>
  </div>
</div>


<div class="form-group col-sm-12">
  <div class="box box-primary">
    <div class="box-body">
      <!-- Route Code Field -->
      <div class="form-group">
        {{ Form::label('route_code', 'Route Code:') }}
        {{ $flight->route_code }}
      </div>

      <!-- Route Leg Field -->
      <div class="form-group">
        {{ Form::label('route_leg', 'Route Leg:') }}
        {{ $flight->route_leg }}
      </div>

      <!-- Alt Airport Id Field -->
      @if($flight->alt_airport_id)
        <div class="form-group">
          {{ Form::label('alt_airport_id', 'Alt Airport Id:') }}
          <p>{{ $flight->alt_airport_id }}</p>
        </div>
    @endif

    <!-- Route Field -->
      <div class="form-group">
        {{ Form::label('route', 'Route:') }}
        <p>{{ $flight->route }}</p>
      </div>

      <!-- Dpt Time Field -->
      <div class="form-group">
        {{ Form::label('dpt_time', 'Departure Time:') }}
        {{ $flight->dpt_time }}
      </div>

      <!-- Arr Time Field -->
      <div class="form-group">
        {{ Form::label('arr_time', 'Arrival Time:') }}
        {{ $flight->arr_time }}
      </div>

      <!-- Notes Field -->
      <div class="form-group">
        {{ Form::label('notes', 'Notes:') }}
        <p>{{ $flight->notes }}</p>
      </div>

      <!-- Active Field -->
      <div class="form-group">
        {{ Form::label('active', 'Active:') }}
        <p>{{ $flight->active }}</p>
      </div>
    </div>
  </div>
</div>
