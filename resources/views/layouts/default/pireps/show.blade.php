@extends('layouts.default.app')

@section('title', 'PIREP '.$pirep->getFlightId())
@section('content')
    <div class="row">
        <div class="col-md-8">
            <h2 class="description">{!! $pirep->getFlightId() !!}</h2>
        </div>
        <div class="col-md-4 align-right">
            @if($pirep->status == config('enums.pirep_status.PENDING'))
                <div class="badge badge-warning "><span class="font-large">Pending</span></div>
            @elseif($pirep->status === config('enums.pirep_status.ACCEPTED'))
                <div class="badge badge-success">Accepted</div>
            @else
                <div class="badge badge-danger">Rejected</div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <tr>
                    <td>Departure/Arrival</td>
                    <td>
                        {!! $pirep->dpt_airport->icao !!} - {!! $pirep->dpt_airport->name !!}
                        <span class="description">to</span>
                        {!! $pirep->arr_airport->icao !!} - {!! $pirep->arr_airport->name !!}
                    </td>
                </tr>

                <tr>
                    <td>Flight Time</td>
                    <td>
                        {!! Utils::minutesToTimeString($pirep->flight_time) !!}
                    </td>
                </tr>

                <tr>
                    <td>Route</td>
                    <td>
                        {!! $pirep->route !!}
                    </td>
                </tr>

                <tr>
                    <td>Notes</td>
                    <td>
                        {!! $pirep->notes !!}
                    </td>
                </tr>

            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3 class="description">fields</h3>
            <table class="table">
                <thead>
                <th>Name</th>
                <th>Value</th>
                <th>Source</th>
                </thead>
                <tbody>
                @foreach($pirep->fields as $field)
                    <tr>
                        <td>{!! $field->name !!}</td>
                        <td>{!! $field->value !!}</td>
                        <td>{!! $field->source !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3 class="description">map</h3>
        </div>
        <div class="col-xs-12">
            <div class="box-body">
                <div id="map" style="width: 100%; height: 800px"></div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
    phpvms_render_airspace_map({
        lat: {!! $pirep->arr_airport->lat !!},
        lon: {!! $pirep->dpt_airport->lon !!},
    });
</script>
@endsection
