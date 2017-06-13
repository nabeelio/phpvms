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
                    <hr/>
                    <h3 class="box-header">fares</h3>
                    <div class="box-body">
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
    $(document).on('submit', 'form.rm_fare', function(event) {
        event.preventDefault();
        $.pjax.submit(event, '#aircraft_fares_wrapper', {push: false});
    });
});
</script>
@endsection
