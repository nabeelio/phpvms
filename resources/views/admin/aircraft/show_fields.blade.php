<!-- Icao Field -->
<div class="form-group col-sm-6">
    {!! Form::label('icao', 'ICAO:') !!}
    <p>{!! $aircraft->icao !!}</p>
</div>

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    <p>{!! $aircraft->name !!}</p>
</div>

<!-- Registration Field -->
<div class="form-group col-sm-6">
    {!! Form::label('registration', 'Registration:') !!}
    <p>{!! $aircraft->registration !!}</p>
</div>

<!-- Active Field -->
<div class="form-group col-sm-6">
    {!! Form::label('active', 'Active:') !!}
    <p>{!! $aircraft->active !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group col-sm-6">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $aircraft->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $aircraft->updated_at !!}</p>
</div>

