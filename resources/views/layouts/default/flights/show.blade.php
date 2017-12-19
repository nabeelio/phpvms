@extends('layouts.default.app')

@section('content')
<div class="row">
    @include('layouts.default.flights.show_fields')
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function () {
    $(".select2_dropdown").select2();

    $(document).on('submit', 'form.pjax_form', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#subfleet_flight_wrapper', {push: false});
    });

    $(document).on('pjax:complete', function() {
        $(".select2_dropdown").select2();
    });
});
</script>
@endsection
