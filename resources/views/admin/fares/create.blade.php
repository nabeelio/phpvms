@extends('admin.app')
@section('title', 'Add Fare')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.fares.store', 'autocomplete' => false]) }}
      @include('admin.fares.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
