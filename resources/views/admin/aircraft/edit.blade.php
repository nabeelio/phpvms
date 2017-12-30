@extends('admin.app')
@section('title', "Edit \"$aircraft->name\"")
@section('content')
<div class="card border-blue-bottom">
   <div class="content">
      @include('admin.flash.message')
       {!! Form::model($aircraft, ['route' => ['admin.aircraft.update', $aircraft->id], 'method' => 'patch']) !!}
            @include('admin.aircraft.fields')
       {!! Form::close() !!}
   </div>
</div>
@endsection
