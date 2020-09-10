@extends('admin.app')
@section('title', 'Editing ' . $field->name)
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($field, ['route' => ['admin.userfields.update', $field->id], 'method' => 'patch', 'autofill' => false]) }}
      @include('admin.userfields.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
