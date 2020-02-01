@extends('admin.app')
@section('title', 'Edit '. $user->name)
@section('content')
  <div class="card">
    <div class="content">
      @include('admin.users.fields')
    </div>
  </div>

@endsection
