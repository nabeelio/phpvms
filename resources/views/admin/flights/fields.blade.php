<!-- Airline Id Field -->
<div class="row">
    <div class="form-group col-sm-3">
        {!! Form::label('airline_id', 'Airline:') !!}&nbsp;<span class="required">*</span>
        {!! Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) !!}
        <p class="text-danger">{{ $errors->first('airline_id') }}</p>
    </div>

    <!-- Flight Number Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('flight_number', 'Flight Number:') !!}&nbsp;<span class="required">*</span>
        {!! Form::text('flight_number', null, ['class' => 'form-control']) !!}
        <p class="text-danger">{{ $errors->first('flight_number') }}</p>
    </div>

    <!-- Route Code Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('route_code', 'Route Code:') !!}
        {!! Form::text('route_code', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Route Leg Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('route_leg', 'Route Leg:') !!}
        {!! Form::text('route_leg', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!--
SAME ROW
-->

<div class="row">
    <!-- Dpt Airport Id Field -->
    <div class="form-group col-sm-4">
        {!! Form::label('dpt_airport_id', 'Departure Airport:') !!}&nbsp;<span class="required">*</span>
        {!! Form::select('dpt_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
        <p class="text-danger">{{ $errors->first('dpt_airport_id') }}</p>
    </div>

    <!-- Arr Airport Id Field -->
    <div class="form-group col-sm-4">
        {!! Form::label('arr_airport_id', 'Arrival Airport:') !!}&nbsp;<span class="required">*</span>
        {!! Form::select('arr_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
        <p class="text-danger">{{ $errors->first('arr_airport_id') }}</p>
    </div>

    <!-- Alt Airport Id Field -->
    <div class="form-group col-sm-4">
        {!! Form::label('alt_airport_id', 'Alt Airport:') !!}
        {!! Form::select('alt_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
    </div>
</div>


<!-- Dpt Time Field -->
<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('dpt_time', 'Departure Time:') !!}
        {!! Form::text('dpt_time', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Arr Time Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('arr_time', 'Arrival Time:') !!}
        {!! Form::text('arr_time', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row">
    <!-- Route Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('route', 'Route:') !!}
        {!! Form::textarea('route', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Notes Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('notes', 'Notes:') !!}
        {!! Form::textarea('notes', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row">
    <!-- Active Field -->
    <div class="col-sm-4">
        {!! Form::label('active', 'Active:') !!}
        @if($flight!==null)
            {!! Form::checkbox('active', $flight->active, ['class' => 'form-control icheck']) !!}
        @else
            {!! Form::checkbox('active', null, ['class' => 'form-control icheck']) !!}
        @endif
    </div>
    <div class="col-8">
        <div class="text-right">
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
            <a href="{!! route('admin.flights.index') !!}" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>
