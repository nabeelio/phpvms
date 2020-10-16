@extends('admin.app')
@section('title', 'Add Download')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($download, ['route' => ['admin.downloads.store', 'autocomplete' => false], 'files' => true]) }}
        @include('admin.downloads.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
@include('admin.downloads.scripts')
