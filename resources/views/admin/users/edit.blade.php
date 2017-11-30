@extends('admin.app')
@section('title', "Edit \"$user->name\"")
@section('content')
<div class="card border-blue-bottom">
   <div class="content">
       @include('adminlte-templates::common.errors')
       {!! Form::model($user, ['route' => ['admin.users.update', $user->id], 'method' => 'patch']) !!}
            @include('admin.users.fields')
       {!! Form::close() !!}
   </div>
</div>
@endsection
