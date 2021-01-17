<h3 class="description">@lang('flights.search')</h3>
<div class="card border-blue-bottom">
  <div class="card-body ml-1 mr-1" style="min-height: 0px; display: flex; justify-content: center; align-items: center;">
    <div class="form-group search-form">
      {{ Form::open([
              'route' => 'frontend.flights.search',
              'method' => 'GET',
              'class'=>'form-inline'
      ]) }}
      <div class="mt-1">
        <p>@lang('common.airline')</p>
        {{ Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) }}
      </div>

      <div class="form-group">
        <label>@lang('flights.flighttype')</label>
        <select id="flight_type" name="flight_type" class="custom-select select2">
          <option value=""></option>
          <option value="J">@lang('flights.type.pass_scheduled')</option>
          <option value="G">@lang('flights.type.pass_addtl')</option>
          <option value="C">@lang('flights.type.charter_pass_only')</option>
          <option value="O">@lang('flights.type.charter_special')</option>
          <option value="E">@lang('flights.type.special_vip')</option>
          <option value="F">@lang('flights.type.cargo_scheduled')</option>
          <option value="A">@lang('flights.type.addtl_cargo_mail')</option>
          <option value="H">@lang('flights.type.charter_cargo')</option>
          <option value="M">@lang('flights.type.mail_service')</option>
          <option value="I">@lang('flights.type.ambulance')</option>
          <option value="K">@lang('flights.type.training_flight')</option>
          <option value="P">@lang('flights.type.positioning')</option>
          <option value="T">@lang('flights.type.technical_test')</option>
          <option value="X">@lang('flights.type.technical_stop')</option>
          <option value="W">@lang('flights.type.military')</option>
        </select>
      </div>

      <div>
        <p>@lang('flights.flightnumber')</p>
        {{ Form::text('flight_number', null, ['class' => 'form-control']) }}
      </div>

      <div class="mt-1">
        <p>@lang('airports.departure')</p>
        {{ Form::select('dep_icao', $airports, null , ['class' => 'form-control select2']) }}
      </div>

      <div class="mt-1">
        <p>@lang('airports.arrival')</p>
        {{ Form::select('arr_icao', $airports, null , ['class' => 'form-control select2']) }}
      </div>

      <div class="mt-1">
        <p>@lang('common.subfleet')</p>
        {{ Form::select('subfleet_id', $subfleets, null , ['class' => 'form-control select2']) }}
      </div>

      <div class="clear mt-1" style="margin-top: 10px;">
        {{ Form::submit(__('common.find'), ['class' => 'btn btn-outline-primary']) }}&nbsp;
        <a href="{{ route('frontend.flights.index') }}">@lang('common.reset')</a>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
