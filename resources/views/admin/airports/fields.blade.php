<div class="row">
<!-- Icao Field -->
<div class="form-group col-sm-6">
    {!! Form::label('icao', 'ICAO:') !!}
    {!! Form::text('icao', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('location', 'Location:') !!}
    {!! Form::text('location', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('lat', 'Latitude:') !!}
    {!! Form::number('lat', null, ['class' => 'form-control', 'step' => '0.000001']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('lon', 'Longitude:') !!}
    {!! Form::number('lon', null, ['class' => 'form-control', 'step' => '0.000001']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('timezone', 'Timezone:') !!}
    {!! Form::select('timezone', $timezones, null, ['id'    => 'timezone', 'class' => 'select2' ]); !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <div class="pull-right">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.airports.index') !!}" class="btn btn-default">Cancel</a>
    </div>
</div>
</div>
