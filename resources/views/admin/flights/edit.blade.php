@extends('admin.app')

@section('title', 'Edit Flight')
@section('content')
<div class="card">
   @include('adminlte-templates::common.errors')
   <div class="content">
        {!! Form::model($flight, ['route' => ['admin.flights.update', $flight->id], 'method' => 'patch']) !!}
            @include('admin.flights.fields')
        {!! Form::close() !!}
   </div>
</div>

<h3>custom fields</h3>
<div class="card">
    <div class="content">
       @include('admin.flights.flight_fields')
    </div>
</div>

<h3>assigned subfleets</h3>
<div class="card">
    <div class="content">
        @include('admin.flights.subfleets')
    </div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function () {

    $('#flight_fields_wrapper a.inline').editable({
        type: 'text',
        mode: 'inline',
        emptytext: '0',
        url: '/admin/flights/{!! $flight->id !!}/fields',
        ajaxOptions: {'type': 'put'},
        params: function(params) {
            return {
                field_id: params.pk,
                name: params.name,
                value: params.value
            }
        }
    });

    $(document).on('submit', 'form.pjax_form', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#flight_fields_wrapper', {push: false});
    });

    $(document).on('pjax:complete', function() {
        $(".select2").select2();
    });
});
</script>
@endsection
