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
   </div>
@endsection
