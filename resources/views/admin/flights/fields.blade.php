<!-- Airline Id Field -->
<div class="row">
    <div class="form-group col-sm-5">
        {!! Form::label('airline_id', 'Airline:') !!}
        {!! Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) !!}
    </div>

    <!-- Flight Number Field -->
    <div class="form-group col-sm-5">
        {!! Form::label('flight_number', 'Flight Number:') !!}
        {!! Form::text('flight_number', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Active Field -->
    <div class="form-group col-sm-2">
        {!! Form::label('active', 'Active:') !!}
        {!! Form::checkbox('active', $flight->active, ['class' => 'form-control icheck']) !!}
    </div>
</div>

<div class="row">
    <!-- Route Code Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('route_code', 'Route Code:') !!}
        {!! Form::text('route_code', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Route Leg Field -->
    <div class="form-group col-sm-6">
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
        {!! Form::label('dpt_airport_id', 'Departure Airport:') !!}
        {!! Form::select('dpt_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
    </div>

    <!-- Arr Airport Id Field -->
    <div class="form-group col-sm-4">
        {!! Form::label('arr_airport_id', 'Arrival Airport:') !!}
        {!! Form::select('arr_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
    </div>

    <!-- Alt Airport Id Field -->
    <div class="form-group col-sm-4">
        {!! Form::label('alt_airport_id', 'Alt Airport:') !!}
        {!! Form::select('alt_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
    </div>
</div>

<!--
END SAME ROW
-->

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
    {!! Form::text('route', null, ['class' => 'form-control']) !!}
</div>

<!-- Notes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('notes', 'Notes:') !!}
    {!! Form::text('notes', null, ['class' => 'form-control']) !!}
</div>
</div>

<div class="row">
    <div class="col-12">
    <div class="row pull-right">
        <div class="form-group col-sm-12 form-inline">
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
            <a href="{!! route('admin.flights.index') !!}" class="btn btn-default">Cancel</a>
        </div>
    </div>
    </div>
</div>
