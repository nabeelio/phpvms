@extends('admin.app')
@section('title', "Edit \"$airport->name\"")
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($airport, [
           'route' => ['admin.airports.update', $airport->id],
           'method' => 'patch',
           'id' => 'airportForm',
           'autocomplete' => false,
           ])
      }}
      @include('admin.airports.fields')
      {{ Form::close() }}
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.airports.expenses')
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.common.file_upload', ['model' => $airport])
    </div>
  </div>
@endsection
@include('admin.airports.script')
