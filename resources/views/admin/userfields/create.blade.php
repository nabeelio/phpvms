@extends('admin.app')
@section('title', 'Adding Field')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.userfields.store', 'autofill' => false]) }}
      @include('admin.userfields.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
