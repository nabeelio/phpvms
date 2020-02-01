@extends('admin.app')

@section('content')
  <section class="content-header">
    <h1 class="pull-left">{{ $airport->name }} - {{ $airport->location }}</h1>
    <h1 class="pull-right">
      <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px"
         href="{{ route('admin.airports.edit', $airport->id) }}">Edit</a>
    </h1>
  </section>
  <section class="content">
    <div class="clearfix"></div>
    <div class="row">
      @include('admin.airports.show_fields')
    </div>
    <div class="box box-primary">
      <div class="box-body">
        <div class="row">
          <div class="col-xs-12">
            <div class="box-body">
              <div id="map" style="width: 100%; height: 800px"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
@section('scripts')
  <script type="text/javascript">
    phpvms_render_airspace_map({
      lat: {{ $airport->lat }},
      lon: {{ $airport->lon }},
    });
  </script>
@endsection
