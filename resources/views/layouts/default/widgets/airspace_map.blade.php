<div id="map" style="width: {{ $config['width'] }}; height: {{ $config['height'] }}"></div>

@section('scripts')
<script>
phpvms.map.render_airspace_map({
    lat: "{{$config['lat']}}",
    lon: "{{$config['lon']}}",
});
</script>
@endsection
