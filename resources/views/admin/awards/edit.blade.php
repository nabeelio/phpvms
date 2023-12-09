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
  @if(filled($owners))
    <div class="card border-blue-bottom">
      @include('admin.awards.owners_table')
    </div>
  @endif
@endsection
@include('admin.awards.scripts')
