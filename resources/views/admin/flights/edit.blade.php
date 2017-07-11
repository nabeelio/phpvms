@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>Edit {!! $flight->airline->code !!}{!! $flight->flight_number !!}</h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($flight, ['route' => ['admin.flights.update', $flight->id], 'method' => 'patch']) !!}

                        @include('admin.flights.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>

       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   <div class="col-xs-12">
                       <h3>custom fields</h3>
                       <div class="box-body">
                           @include('admin.flights.flight_fields')
                       </div>
                   </div>
               </div>
           </div>
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

    $(".select2_dropdown").select2();

    $(document).on('submit', 'form.pjax_form', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#flight_fields_wrapper', {push: false});
    });

    $(document).on('pjax:complete', function() {
        $(".select2_dropdown").select2();
    });
});
</script>
@endsection
