@extends('admin.app')

@section('content')
<section class="content-header"><h1>{!! $aircraft->name !!}</section>
<section class="content">
    <div class="row">
        @include('admin.aircraft.show_fields')
    </div>
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12">
                    <h3>fares</h3>
                    <div class="box-body">
                        <div class="callout callout-info">
                            <i class="icon fa fa-info">&nbsp;&nbsp;</i>
                            Fares assigned to the current aircraft. These can be overridden,
                            otherwise, the value used is the default, which comes from the fare.
                        </div>
                        @include('admin.aircraft.fares')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
    $(".ac-fare-dropdown").select2();
    $('#aircraft_fares a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'default',
        url: '/admin/aircraft/{!! $aircraft->id !!}/fares',
        title: 'Enter override value',
        ajaxOptions: { 'type': 'put'},
        params: function(params) {
          return {
              fare_id: params.pk,
              name: params.name,
              value: params.value
          }
        }
    });

    $(document).on('submit', 'form.rm_fare', function(event) {
        event.preventDefault();
        $.pjax.submit(event, '#aircraft_fares_wrapper', {push: false});
    });
});
</script>
@endsection
