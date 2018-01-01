@extends('admin.app')
@section('title', 'Editing ' . $field->name)
@section('content')
<div class="card border-blue-bottom">
    <div class="content">
        @include('admin.flash.message')
       {!! Form::model($field, ['route' => ['admin.pirepfields.update', $field->id], 'method' => 'patch']) !!}
            @include('admin.pirepfields.fields')
       {!! Form::close() !!}
   </div>
</div>
@endsection
