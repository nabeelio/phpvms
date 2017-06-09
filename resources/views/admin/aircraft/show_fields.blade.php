<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'ID:') !!}
    <p>{!! $aircraft->id !!}</p>
</div>

<!-- Icao Field -->
<div class="form-group">
    {!! Form::label('icao', 'ICAO:') !!}
    <p>{!! $aircraft->icao !!}</p>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{!! $aircraft->name !!}</p>
</div>

<!-- Full Name Field -->
<div class="form-group">
    {!! Form::label('full_name', 'Full Name:') !!}
    <p>{!! $aircraft->full_name !!}</p>
</div>

<!-- Registration Field -->
<div class="form-group">
    {!! Form::label('registration', 'Registration:') !!}
    <p>{!! $aircraft->registration !!}</p>
</div>

<!-- Active Field -->
<div class="form-group">
    {!! Form::label('active', 'Active:') !!}
    <p>{!! $aircraft->active !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $aircraft->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $aircraft->updated_at !!}</p>
</div>

