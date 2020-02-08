@extends('admin.app')

@section('content')
  <section class="content-header">
    <h1 class="pull-left">{{ $aircraft->name }}</h1>
    <h1 class="pull-right">
      <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px"
         href="{{ route('admin.aircraft.edit', $aircraft->id) }}">Edit</a>
    </h1>
  </section>
  <section class="content">
    <div class="clearfix"></div>
    <div class="row">
      @include('admin.aircraft.show_fields')
    </div>
  </section>
@endsection
@section('scripts')
  <script>
    $(document).ready(function () {
      $(".ac-fare-dropdown").select2();
      $('#aircraft_fares a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'default',
        url: '/admin/aircraft/{{ $aircraft->id }}/fares',
        title: 'Enter override value',
        ajaxOptions: {'type': 'put'},
        params: function (params) {
          return {
            fare_id: params.pk,
            name: params.name,
            value: params.value
          }
        }
      });

      $(document).on('submit', 'form.rm_fare', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#aircraft_fares_wrapper', {push: false});
      });
    });
  </script>
@endsection
