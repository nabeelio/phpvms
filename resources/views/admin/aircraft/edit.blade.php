@extends('admin.app')
@section('title', "Edit \"$aircraft->name\"")
@section('content')
   <div class="card border-blue-bottom">
      <div class="content">
          {{ Form::model($aircraft, ['route' => ['admin.aircraft.update', $aircraft->id], 'method' => 'patch']) }}
               @include('admin.aircraft.fields')
          {{ Form::close() }}
      </div>
   </div>

   <div class="card border-blue-bottom">
      <div class="content">
         @include('admin.aircraft.expenses')
      </div>
   </div>

   <div class="card border-blue-bottom">
      <div class="content">
         @include('admin.common.file_upload', [
            'model'    => $aircraft,
            'redirect' => route('admin.aircraft.edit', ['id' => $aircraft->id])
         ])
      </div>
   </div>
@endsection

@include('admin.aircraft.script')
