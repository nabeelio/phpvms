@extends('admin.app')
@section('title', "Edit \"$airline->name\"")
@section('content')
<div class="card border-blue-bottom">
   <div class="content">
       @include('adminlte-templates::common.errors')
       {!! Form::model($airline, ['route' => ['admin.airlines.update', $airline->id], 'method' => 'patch']) !!}
            @include('admin.airlines.fields')
       {!! Form::close() !!}
   </div>
</div>
@endsection
