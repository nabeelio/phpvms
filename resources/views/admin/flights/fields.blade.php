<!-- Airline Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('airline_id', 'Airline:') !!}
    {!! Form::text('airline_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Flight Number Field -->
<div class="form-group col-sm-6">
    {!! Form::label('flight_number', 'Flight Number:') !!}
    {!! Form::text('flight_number', null, ['class' => 'form-control']) !!}
</div>

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

<!-- Dpt Airport Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dpt_airport_id', 'Dpt Airport Id:') !!}
    {!! Form::text('dpt_airport_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Arr Airport Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('arr_airport_id', 'Arr Airport Id:') !!}
    {!! Form::text('arr_airport_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Alt Airport Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('alt_airport_id', 'Alt Airport Id:') !!}
    {!! Form::text('alt_airport_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Route Field -->
<div class="form-group col-sm-6">
    {!! Form::label('route', 'Route:') !!}
    {!! Form::text('route', null, ['class' => 'form-control']) !!}
</div>

<!-- Dpt Time Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dpt_time', 'Dpt Time:') !!}
    {!! Form::text('dpt_time', null, ['class' => 'form-control']) !!}
</div>

<!-- Arr Time Field -->
<div class="form-group col-sm-6">
    {!! Form::label('arr_time', 'Arr Time:') !!}
    {!! Form::text('arr_time', null, ['class' => 'form-control']) !!}
</div>

<!-- Notes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('notes', 'Notes:') !!}
    {!! Form::text('notes', null, ['class' => 'form-control']) !!}
</div>

<!-- Active Field -->
<div class="form-group col-sm-6">
    {!! Form::label('active', 'Active:') !!}
    {!! Form::text('active', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.flights.index') !!}" class="btn btn-default">Cancel</a>
</div>
