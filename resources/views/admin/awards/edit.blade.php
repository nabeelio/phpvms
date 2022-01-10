@extends('admin.app')
@section('title', "Edit \"$award->name\" Award")
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($award, ['route' => ['admin.awards.update', $award->id], 'method' => 'patch', 'autocomplete' => false]) }}
      @include('admin.awards.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
@include('admin.awards.scripts')
