@extends('admin.app')
@section('title', 'PIREP Fields')
@section('actions')
  <li><a href="{{ route('admin.pirepfields.create') }}"><i class="ti-plus"></i>Add
      Field</a></li>
@endsection
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.pirepfields.table')
    </div>
  </div>
@endsection

