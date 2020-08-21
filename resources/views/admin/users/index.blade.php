@extends('admin.app')

@section('title', 'Users')
@section('actions')
  <li><a href="{{ route('admin.userfields.index') }}">
      <i class="ti-user"></i>Profile Fields</a>
  </li>

  <li><a href="{{ route('admin.users.index') }}?search=state:0">
      <i class="ti-user"></i>@lang(UserState::label(UserState::PENDING))</a>
  </li>
@endsection

@section('content')
  <div class="card">
    @include('admin.users.search')
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.users.table')
    </div>
  </div>

  <div class="row">
    <div class="col-12 text-center">
      {{ $users->links('admin.pagination.default') }}
    </div>
  </div>
@endsection

