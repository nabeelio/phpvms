@extends('admin.app')
@section('title', 'Editing ' . $field->name)
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($field, ['route' => ['admin.flightfields.update', $field->id], 'method' => 'patch', 'autocomplete' => false]) }}
      @include('admin.flightfields.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
