@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>Edit {!! $flight->airline->name !!}{!! $flight->number !!}</h1>
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
