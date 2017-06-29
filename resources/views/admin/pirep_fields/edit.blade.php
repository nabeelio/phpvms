@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>Edit "{!! $field->name !!}"</h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($field, ['route' => ['admin.pirepfields.update', $field->id], 'method' => 'patch']) !!}
                        @include('admin.pirep_fields.fields')
                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection
