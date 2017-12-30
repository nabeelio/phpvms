@extends('admin.app')

@section('title', "Edit $subfleet->name")
@section('content')
    @include('admin.flash.message')
   <div class="card border-blue-bottom">
       <div class="content">
           {!! Form::model($subfleet, ['route' => ['admin.subfleets.update', $subfleet->id], 'method' => 'patch']) !!}
            @include('admin.subfleets.fields')
           {!! Form::close() !!}
       </div>
   </div>
   <div class="card border-blue-bottom">
       <div class="header">
           <h3>fares</h3>
           <p class="category">
               <i class="icon fa fa-info">&nbsp;&nbsp;</i>
               Fares assigned to the current subfleet. These can be overridden,
               otherwise, the value used is the default, which comes from the fare.
           </p>
       </div>
       <div class="content">
           <div class="row">
               <div class="col-xs-12">
                   @include('admin.subfleets.fares')
               </div>
           </div>
       </div>
   </div>
@endsection
@include('admin.subfleets.script')
