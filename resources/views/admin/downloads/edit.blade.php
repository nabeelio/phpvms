@extends('admin.app')

@section('title', 'Edit Download')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($download, ['route' => ['admin.downloads.update', $download->id], 'method' => 'patch', 'autocomplete' => false, 'files' => true]) }}
      @include('admin.downloads.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
@include('admin.downloads.scripts')
