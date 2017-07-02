@extends('admin.app')

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
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   <div class="col-xs-12">
                       <h3>fares</h3>
                       <div class="box-body">
                           <div class="callout callout-info">
                               <i class="icon fa fa-info">&nbsp;&nbsp;</i>
                               Fares assigned to the current subfleet. These can be overridden,
                               otherwise, the value used is the default, which comes from the fare.
                           </div>
                           @include('admin.subfleets.fares')
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
@endsection
@include('admin.subfleets.script')
