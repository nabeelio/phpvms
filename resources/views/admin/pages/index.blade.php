@extends('admin.app')

@section('title', 'Pages')
@section('actions')
  <li>
    <a href="{{ route('admin.pages.create') }}">
      <i class="ti-plus"></i>
      Add New</a>
  </li>
@endsection

@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.pages.table')
    </div>
  </div>
@endsection

