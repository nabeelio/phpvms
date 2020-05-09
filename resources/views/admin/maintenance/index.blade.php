@extends('admin.app')

@section('title', 'Maintenance')
@section('content')
  @include('flash::message')

  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.maintenance.update')
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.maintenance.cron')
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.maintenance.caches')
    </div>
  </div>
@endsection
