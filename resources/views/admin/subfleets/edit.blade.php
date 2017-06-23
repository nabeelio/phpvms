@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>Edit {!! $subfleet->name !!}</h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($subfleet, ['route' => ['admin.subfleets.update', $subfleet->id], 'method' => 'patch']) !!}

                        @include('admin.subfleets.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection
