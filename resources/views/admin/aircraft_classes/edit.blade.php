@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Aircraft Class
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($aircraftClass, ['route' => ['admin.aircraftClasses.update', $aircraftClass->id], 'method' => 'patch']) !!}

                        @include('admin.aircraft_classes.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection