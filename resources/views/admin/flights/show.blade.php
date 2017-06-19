@extends('admin.app')

@section('content')
<section class="content-header">
    <h1 class="pull-left">{!! $flight->airline->code !!}{!! $flight->flight_number !!}</h1>
    <h1 class="pull-right">
        <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px"
           href="{!! route('admin.flights.edit', $flight->id) !!}">Edit</a>
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
                    <h3>assigned aircraft</h3>
                    <div class="box-body">
                        @include('admin.flights.aircraft')
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
        $(".ac-flight-dropdown").select2();

        $(document).on('submit', 'form.flight_ac_frm', function (event) {
            event.preventDefault();
            $.pjax.submit(event, '#flight_aircraft_wrapper', {push: false});
        });
    });
</script>
@endsection
