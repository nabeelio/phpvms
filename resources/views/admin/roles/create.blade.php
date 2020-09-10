@extends('admin.app')
@section('title', 'Add Role')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.roles.store', 'autocomplete' => false]) }}
      @include('admin.roles.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
