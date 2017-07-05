<!-- Id Field -->
<!-- Airline Id Field -->
<div class="form-group">
    {!! Form::label('airline_id', 'Airline Id:') !!}
    <p>{!! $subfleet->airline->name !!}</p>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{!! $subfleet->name !!}</p>
</div>

<!-- Type Field -->
<div class="form-group">
    {!! Form::label('type', 'Type:') !!}
    <p>{!! $subfleet->type !!}</p>
</div>

<!-- Fuel Type Field -->
<div class="form-group">
    {!! Form::label('fuel_type', 'Fuel Type:') !!}
    <p>
    @if($subfleet->fuel_type === config('enums.fuel_types.100LL'))
        100LL
    @elseif($subfleet->fuel_type === config('enums.fuel_types.JETA'))
        JETA
    @elseif($subfleet->fuel_type === config('enums.fuel_types.MOGAS'))
        MOGAS
    @else
        -
    @endif
    </p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $subfleet->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $subfleet->updated_at !!}</p>
</div>

