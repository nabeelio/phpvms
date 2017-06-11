<!-- Icao Field -->
<div class="form-group">
    {!! Form::label('icao', 'ICAO:') !!}
    <p>{!! $airport->icao !!}</p>
</div>

<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{!! $airport->name !!}</p>
</div>

<div class="form-group">
    {!! Form::label('location', 'Location:') !!}
    <p>{!! $airport->location !!}</p>
</div>

<div class="form-group">
    {!! Form::label('lat', 'Latitude:') !!}
    <p>{!! $airport->lat !!}</p>
</div>

<div class="form-group">
    {!! Form::label('lon', 'Longitude:') !!}
    <p>{!! $airport->lon !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $airport->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $airport->updated_at !!}</p>
</div>

