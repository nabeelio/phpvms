<div class="row">
    <div class="col-md-12">
        <div class="box-body">

            <div id="map" style="width: {{ $config['width'] }}; height: {{ $config['height'] }}">
                <div id="map-info-bar"
                     style="display: none;
                            position: absolute;
                            bottom: 0;
                            padding: 20px;
                            height: 100px;
                            z-index: 9999;
                            background-color:rgba(232, 232, 232, 0.9);
                            width: {{ $config['width'] }};">
                    <div style="float: left; margin-right: 30px; width: 50%;">
                        <h3 style="margin: 0" id="map_flight_id"></h3>
                        <p id="map_flight_info"></p>
                    </div>
                    <div style="float: left; margin-right: 30px;">
                        <p id="map_flight_stats_middle"></p>
                    </div>
                    <div style="float: left;">
                        <p id="map_flight_stats_right"></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="clearfix" style="padding-top: 25px"></div>

<div id="flights_table" class="row">
    <div class="col-md-12">
        @if(!filled($pireps))
            <div class="jumbotron text-center">There are no flights</div>
        @endif
        <table class="table">
            @foreach($pireps as $pirep)
                <tr>
                    <td>{{ $pirep->airline->code }}{{ $pirep->ident }}</td>
                    <td>{{ $pirep->dpt_airport_id }}</td>
                    <td>{{ $pirep->arr_airport_id }}</td>
                    <td>{{ $pirep->aircraft->name }}</td>
                    <td>
                        {{ PirepStatus::label($pirep->status) }}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>

@section('scripts')
<script>
phpvms.map.render_live_map({
    'update_uri': '{!! url('/api/acars') !!}',
    'pirep_uri': '{!! url('/api/pireps/{id}') !!}',
    'aircraft_icon': '{!! public_asset('/assets/img/acars/aircraft.png') !!}',
    'units': '{{ setting('units.distance') }}',
});
</script>
@endsection
