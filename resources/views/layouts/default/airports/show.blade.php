@extends('app')
@section('title', $airport->full_name)

@section('content')
<div class="row" style="margin-bottom: 30px;">
    <div class="col-12">
        <h2 class="description">{{ $airport->full_name }}</h2>
    </div>

    {{-- Show the weather widget in one column --}}
    <div class="col-5">
        {{ Widget::checkWx([
            'icao' => $airport->icao,
          ]) }}
    </div>

    {{-- Show the airspace map in the other column --}}
    <div class="col-7">
        {{ Widget::airspaceMap([
            'width' => '100%',
            'height' => '400px',
            'lat' => $airport->lat,
            'lon' => $airport->lon,
          ]) }}
    </div>
</div>
<div class="row" style="margin-bottom: 30px;">
    {{-- There are files uploaded and a user is logged in--}}
    @if($airport->files && Auth::check())
        <div class="col-12">
            <h3 class="description">Downloads</h3>
            @include('files.table', ['files' => $airport->files])
        </div>
    @endif
</div>
<div class="row">
    <div class="col-md-12">
        <h3 class="description">Inbound Flights</h3>
        @if(!$inbound_flights)
            <div class="jumbotron text-center">
                no flights found
            </div>
        @else
            @each('airports.table', $inbound_flights, 'flight')
        @endif
        <h3 class="description">Outbound Flights</h3>
        @if(!$outbound_flights)
            <div class="jumbotron text-center">
                no flights found
            </div>
        @else
            @each('airports.table', $outbound_flights, 'flight')
        @endif
    </div>
</div>
@endsection
