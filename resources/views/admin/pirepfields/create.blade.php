@extends('admin.app')
@section('title', 'Adding Field')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.pirepfields.store', 'autocomplete' => false]) }}
      @include('admin.pirepfields.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
