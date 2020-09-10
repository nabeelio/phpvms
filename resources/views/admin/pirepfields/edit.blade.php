@extends('admin.app')
@section('title', 'Editing ' . $field->name)
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($field, ['route' => ['admin.pirepfields.update', $field->id], 'method' => 'patch', 'autocomplete' => false]) }}
      @include('admin.pirepfields.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
