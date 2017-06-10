@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>
            Fare
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($fare, ['route' => ['admin.fares.update', $fare->id], 'method' => 'patch']) !!}

                        @include('admin.fares.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection
