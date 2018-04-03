@extends('app')
@section('title', 'Flight '.$flight->ident)

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="description">{{ $flight->ident }}</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table">
            <tr>
                <td>Departure</td>
                <td>
                    <a href="{{route('frontend.airports.show', ['id'=>$flight->dpt_airport_id])}}">
                        {{ $flight->dpt_airport->full_name }}</a>
                    @ {{ $flight->dpt_time }}</td>
            </tr>

            <tr>
                <td>Arrival</td>
                <td>
                    <a href="{{route('frontend.airports.show', ['id'=>$flight->arr_airport_id])}}">
                        {{ $flight->arr_airport->full_name }}</a> @ {{ $flight->arr_time }}</td>
            </tr>
            @if($flight->alt_airport_id)
            <tr>
                <td>Alternate Airport</td>
                <td>
                    {{ $flight->alt_airport->full_name }}
                </td>
            </tr>
            @endif

            <tr>
                <td>Route</td>
                <td>{{ $flight->route }}</td>
            </tr>

            <tr>
                <td>Notes</td>
                <td>{{ $flight->notes }}</td>
            </tr>
        </table>
    </div>
</div>
<div style="padding: 10px;"></div>
<div class="row">
    <div class="col-6">
        <h5>{{$flight->dpt_airport_id}} METAR</h5>
        {{ Widget::Weather([
            'icao' => $flight->dpt_airport_id,
          ]) }}
    </div>
    <div class="col-6">
        <h5>{{$flight->arr_airport_id}} METAR</h5>
        {{ Widget::Weather([
            'icao' => $flight->arr_airport_id,
          ]) }}
    </div>
</div>
@include('flights.map')
@endsection
