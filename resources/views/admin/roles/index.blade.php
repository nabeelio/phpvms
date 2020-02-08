@extends('admin.app')

@section('title', 'Roles')
@section('actions')
  <li>
    <a href="{{ route('admin.roles.create') }}">
      <i class="ti-plus"></i>
      Add New</a>
  </li>
@endsection

@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.roles.table')
    </div>
  </div>
@endsection

