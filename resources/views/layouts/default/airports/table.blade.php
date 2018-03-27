<table class="table">
    <tr>
        <td>
            <a href="{{ route('frontend.flights.show', [$flight->id]) }}">
                {{ $flight->ident }}
            </a>
        </td>
        <td>{{ $flight->dpt_airport->icao }}</td>
        <td>
            {{ $flight->arr_airport->icao }}
            @if($flight->alt_airport)
                (Alt: {{ $flight->alt_airport->icao }})
            @endif
        </td>
        {{--<td>{{ $flight->route }}</td>--}}
        <td>{{ $flight->dpt_time }}</td>
        <td>{{ $flight->arr_time }}</td>
        <td>{{ $flight->notes }}</td>
</table>
