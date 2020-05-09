@extends('admin.app')
@section('title', "Edit \"$page->name\"")
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($page, ['route' => ['admin.pages.update', $page->id], 'method' => 'patch']) }}
      @include('admin.pages.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
@include('admin.pages.scripts')
