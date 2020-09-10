@extends('admin.app')
@section('title', 'Create PIREP')

<div class="content">
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.pireps.store', 'autocomplete' => false]) }}
      @include('admin.pireps.fields')
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection
@include('admin.pireps.scripts')
