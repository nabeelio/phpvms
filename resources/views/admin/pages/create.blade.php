@extends('admin.app')
@section('title', 'Add Page')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.pages.store', 'autocomplete' => false]) }}
      @include('admin.pages.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
@include('admin.pages.scripts')
