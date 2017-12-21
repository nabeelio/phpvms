@extends('layouts.default.app')
@section('title', 'Flight '.$flight->ident)

@section('content')
<div class="row">
    <div class="col-md-12">
        <h3 class="description">{!! $flight->ident !!} - {!! $flight->dpt_airport->full_name !!} to {!! $flight->arr_airport->full_name !!}</h3>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table">
            <tr>
                <td>Departure</td>
                <td>{!! $flight->dpt_airport->icao !!} @ {!! $flight->dpt_time !!}</td>
            </tr>

            <tr>
                <td>Arrival</td>
                <td>{!! $flight->arr_airport->icao !!} @ {!! $flight->arr_time !!}</td>
            </tr>

            <tr>
                <td>Route Code/Leg:</td>
                <td>{!! $flight->route_code ?: '-' !!}/{!! $flight->route_leg ?: '-' !!}</td>
            </tr>

            <tr>
                <td>Alternate Airport</td>
                <td>
                    @if($flight->alt_airport_id)
                        {!! $flight->alt_airport->full_name !!}
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <td>Route</td>
                <td>{!! $flight->route !!}</td>
            </tr>

            <tr>
                <td>Notes</td>
                <td>{!! $flight->notes !!}</td>
            </tr>
        </table>
    </div>
</div>
@include('layouts.default.flights.map')
@endsection
