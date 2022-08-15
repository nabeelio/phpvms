@extends('admin.app')

@section('title', 'Users')
@section('actions')
  <li>
    <a href="{{ route('admin.userfields.index') }}"></i>Profile Fields</a>
  </li>
  <li>
    <a href="{{ route('admin.users.index') }}?state=0">@lang(UserState::label(UserState::PENDING))</a>
  </li>
  <li>
    <a href="{{ route('admin.users.index') }}?state=1">@lang(UserState::label(UserState::ACTIVE))</a>
  </li>
  <li>
    <a href="{{ route('admin.users.index') }}?state=2">@lang(UserState::label(UserState::REJECTED))</a>
  </li>
  <li>
    <a href="{{ route('admin.users.index') }}?state=3">@lang(UserState::label(UserState::ON_LEAVE))</a>
  </li>
  <li>
    <a href="{{ route('admin.users.index') }}?state=4">@lang(UserState::label(UserState::SUSPENDED))</a>
  </li>
  <li>
    <a href="{{ route('admin.users.index') }}?state=5">@lang(UserState::label(UserState::DELETED))</a>
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

