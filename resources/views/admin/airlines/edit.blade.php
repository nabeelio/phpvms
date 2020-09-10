@extends('admin.app')
@section('title', "Edit \"$airline->name\"")
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($airline, [
            'route' => ['admin.airlines.update', $airline->id],
            'method' => 'patch',
            'autocomplete' => false,
            ]) }}
      @include('admin.airlines.fields')
      {{ Form::close() }}
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.common.file_upload', ['model' => $airline])
    </div>
  </div>
@endsection
