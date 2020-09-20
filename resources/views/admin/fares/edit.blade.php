@extends('admin.app')
@section('title', "Edit \"$fare->name\"")
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($fare, ['route' => ['admin.fares.update', $fare->id], 'method' => 'patch', 'autocomplete' => false]) }}
      @include('admin.fares.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
