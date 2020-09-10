@extends('admin.app')
@section('title', "Edit \"$role->display_name\"")
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($role, ['route' => ['admin.roles.update', $role->id], 'method' => 'patch', 'autocomplete' => false]) }}
      @include('admin.roles.fields')
      {{ Form::close() }}
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.roles.users')
    </div>
  </div>
@endsection
