@extends('admin.app')
@section('title', 'Add Airline')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.airlines.store', 'autocomplete' => false]) }}
      @include('admin.airlines.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
