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
        {!! Form::text('route_code', null, ['class'=>'form-control', 'placeholder'=>'optional']) !!}
        <p class="text-danger">{{ $errors->first('route_code') }}</p>
    </div>

    <!-- Route Leg Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('route_leg', 'Route Leg:') !!}
        {!! Form::text('route_leg', null, ['class'=>'form-control', 'placeholder'=>'optional']) !!}
        <p class="text-danger">{{ $errors->first('route_leg') }}</p>
    </div>
</div>

<!--
SAME ROW
-->

<div class="row">

    <div class="form-group col-sm-3">
        {!! Form::label('level', 'Flight Type:') !!}
        {!! Form::select('flight_type', $flight_types, null, ['class' => 'form-control select2']) !!}
        <p class="text-danger">{{ $errors->first('flight_type') }}</p>
    </div>

    <div class="form-group col-sm-3">
        {!! Form::label('dpt_airport_id', 'Departure Airport:') !!}&nbsp;<span class="required">*</span>
        {!! Form::select('dpt_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
        <p class="text-danger">{{ $errors->first('dpt_airport_id') }}</p>
    </div>

    <!-- Arr Airport Id Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('arr_airport_id', 'Arrival Airport:') !!}&nbsp;<span class="required">*</span>
        {!! Form::select('arr_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
        <p class="text-danger">{{ $errors->first('arr_airport_id') }}</p>
    </div>

    <!-- Alt Airport Id Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('alt_airport_id', 'Alt Airport:') !!}
        {!! Form::select('alt_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
    </div>
</div>


<div class="row">

    <div class="form-group col-sm-3">
        {!! Form::label('dpt_time', 'Departure Time:') !!}
        {!! Form::text('dpt_time', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group col-sm-3">
        {!! Form::label('arr_time', 'Arrival Time:') !!}
        {!! Form::text('arr_time', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group col-sm-2">
        {!! Form::label('level', 'Flight Level:') !!}
        {!! Form::text('level', null, ['class' => 'form-control']) !!}
        <p class="text-danger">{{ $errors->first('level') }}</p>
    </div>

    <div class="form-group col-sm-2">
        {!! Form::label('distance', 'Distance:') !!}
        {!! Form::text('distance', null, ['class' => 'form-control']) !!}
        <p class="text-danger">{{ $errors->first('distance') }}</p>
    </div>

    <div class="form-group col-sm-2">
        {!! Form::label('level', 'Flight Level:') !!}
        {!! Form::text('level', null, ['class' => 'form-control']) !!}
        <p class="text-danger">{{ $errors->first('level') }}</p>
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
            {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
            <a href="{!! route('admin.flights.index') !!}" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>
