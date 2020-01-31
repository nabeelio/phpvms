@extends('admin.app')

@section('content')
  <section class="content-header">
    <h1 class="pull-left">{{ $flight->airline->code }}{{ $flight->flight_number }}</h1>
    <h1 class="pull-right">
      <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px"
         href="{{ route('admin.flights.edit', $flight->id) }}">Edit</a>
    </h1>
  </section>
  <section class="content">
    <div class="clearfix"></div>
    <div class="row">
      @include('admin.flights.show_fields')
    </div>
    <div class="box box-primary">
      <div class="box-body">
        <div class="row">
          <div class="col-xs-12">
            <h3>assigned subfleets</h3>
            <div class="box-body">
              @include('admin.flights.subfleets')
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
@section('scripts')
  <script>
    $(document).ready(function () {
      $(".select2_dropdown").select2();

      $(document).on('submit', 'form.pjax_form', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#subfleet_flight_wrapper', {push: false});
      });

      $(document).on('pjax:complete', function () {
        $(".select2_dropdown").select2();
      });
    });
  </script>
@endsection
