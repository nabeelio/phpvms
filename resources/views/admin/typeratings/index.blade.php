@extends('admin.app')
@section('title', 'Type Ratings')
@section('actions')
  <li>
    <a href="{{ route('admin.typeratings.create') }}">
      <i class="ti-plus"></i>
      Add New
    </a>
  </li>
@endsection

@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.typeratings.table')
    </div>
  </div>
@endsection
