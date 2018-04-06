@extends('app')
@section('title', 'Flight '.$flight->ident)

@section('content')
<div class="row">
    <div class="col-8">
        <div class="row">
            <div class="col-12">
                <h2>{{ $flight->ident }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <tr>
                        <td>Departure</td>
                        <td>
                            {{ $flight->dpt_airport->name }}
                            (<a href="{{route('frontend.airports.show', [
                            'id' => $flight->dpt_airport->icao
                            ])}}">{{$flight->dpt_airport->icao}}</a>)
                            @ {{ $flight->dpt_time }}
                        </td>
                    </tr>

                    <tr>
                        <td>Arrival</td>
                        <td>
                            {{ $flight->arr_airport->name }}
                            (<a href="{{route('frontend.airports.show', [
                            'id' => $flight->arr_airport->icao
                            ])}}">{{$flight->arr_airport->icao}}</a>)
                            @ {{ $flight->arr_time }}</td>
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

                    @if(filled($flight->notes))
                        <tr>
                            <td>Notes</td>
                            <td>{{ $flight->notes }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @include('flights.map')
            </div>
        </div>
    </div>
    <div class="col-4">
        <h5>{{$flight->dpt_airport_id}} METAR</h5>
        {{ Widget::Weather([
            'icao' => $flight->dpt_airport_id,
          ]) }}

        <h5>{{$flight->arr_airport_id}} METAR</h5>
        {{ Widget::Weather([
            'icao' => $flight->arr_airport_id,
          ]) }}
    </div>
</div>
@endsection
