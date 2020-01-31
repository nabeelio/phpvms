@extends('admin.app')
@section('title', 'pilot report')

@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.pireps.show_fields')
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      <h4>custom fields</h4>
      @include('admin.pireps.field_values')
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      <h4>comments</h4>
      @include('admin.pireps.comments')
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.pireps.map')
    </div>
  </div>
@endsection
@include('admin.pireps.scripts')
