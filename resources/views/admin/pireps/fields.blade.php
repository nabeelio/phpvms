<!-- Flight Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('flight_id', 'Flight ID:') !!}
    {!! Form::text('flight_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Aircraft Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('aircraft_id', 'Aircraft ID:') !!}
    {!! Form::text('aircraft_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Flight Time Field -->
<div class="form-group col-sm-6">
    {!! Form::label('flight_time', 'Flight Time:') !!}
    {!! Form::text('flight_time', null, ['class' => 'form-control']) !!}
</div>

<!-- Level Field -->
<div class="form-group col-sm-6">
    {!! Form::label('level', 'Flight Level:') !!}
    {!! Form::text('level', null, ['class' => 'form-control']) !!}
</div>

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

<!-- Raw Data Field -->
<div class="form-group col-sm-6">
    {!! Form::label('raw_data', 'Raw Data:') !!}
    {!! Form::text('raw_data', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.pireps.index') !!}" class="btn btn-default">Cancel</a>
</div>
