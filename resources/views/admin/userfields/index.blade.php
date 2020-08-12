@extends('admin.app')
@section('title', 'User Fields')
@section('actions')
  <li><a href="{{ route('admin.userfields.create') }}"><i class="ti-plus"></i>Add Field</a></li>
@endsection
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.userfields.table')
    </div>
  </div>
@endsection

