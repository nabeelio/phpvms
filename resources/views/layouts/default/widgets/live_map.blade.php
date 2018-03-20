<div class="row">
    <div class="col-md-12">
        <h2 class="description">Current Flights</h2>
        <div class="box-body">
            <div id="map" style="width: {{ $config['width'] }}; height: {{ $config['height'] }}"></div>
        </div>
    </div>
</div>

<div class="clearfix" style="padding-top: 25px"></div>

<div id="flights_table" class="row">
    <div class="col-md-12">
        <h3 class="description">flights</h3>
        @if(!filled($pireps))
            <div class="text-center">There are no flights</div>
        @endif
        <table class="table">
            @foreach($pireps as $pirep)
                <tr>
                    <td>{{ $pirep->ident }}</td>
                    <td>{{ $pirep->dpt_airport_id }}</td>
                    <td>{{ $pirep->arr_airport_id }}</td>
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
    'pirep_uri': '{!! url('/api/pireps/{id}/acars/geojson') !!}',
    'aircraft_icon': '{!! public_asset('/assets/img/acars/aircraft.png') !!}',
});
</script>
@endsection
