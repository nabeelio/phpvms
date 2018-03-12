@extends('admin.app')
@section('title', "Edit \"$airline->name\"")
@section('content')
<div class="card border-blue-bottom">
   <div class="content">
       {{ Form::model($airline, ['route' => ['admin.airlines.update', $airline->id], 'method' => 'patch']) }}
            @include('admin.airlines.fields')
       {{ Form::close() }}
   </div>
</div>
@endsection
