<div class="row">
    <div class="col-md-12">
        <h3 class="description">flight map</h3>
    </div>
    <div class="col-12">
        <div class="box-body">
            <div id="map" style="width: 100%; height: 800px"></div>
        </div>
    </div>
</div>

@section('scripts')
<script type="text/javascript">
phpvms.render_live_map({
    'update_uri': '{!! url('/api/acars') !!}',
    'aircraft_icon': '{!! public_asset('/assets/img/acars/aircraft.png') !!}',
});
</script>
@endsection
