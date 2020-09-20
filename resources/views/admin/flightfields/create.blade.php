@extends('admin.app')
@section('title', 'Adding Field')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.flightfields.store', 'autocomplete' => false]) }}
      @include('admin.flightfields.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
