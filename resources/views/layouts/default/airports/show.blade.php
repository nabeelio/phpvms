@extends('app')
@section('title', $airport->full_name)

@section('content')
<div class="row">
    <div class="col-md-6">
        <h2 class="description">{{ $airport->full_name }}</h2>
    </div>
    <div class="col-md-6">
        {{ Widget::airspaceMap([
            'width' => '100%',
            'height' => '250px',
            'lat' => $airport->lat,
            'lon' => $airport->lon,
          ]) }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h3 class="description">Inbound Flights</h3>
        @if(!$inbound_flights)
            <div class="mini-splash">
                no flights found
            </div>
        @else
            @each('airports.table', $inbound_flights, 'flight')
        @endif

        <h3 class="description">Outbound Flights</h3>
        @each('airports.table', $outbound_flights, 'flight')
    </div>
</div>
@endsection
