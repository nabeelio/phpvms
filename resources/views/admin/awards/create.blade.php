@extends('admin.app')
@section('title', 'Add Award')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.awards.store', 'autocomplete' => false]) }}
      @include('admin.awards.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
@include('admin.awards.scripts')
