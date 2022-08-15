@extends('admin.app')
@section('title', 'Add Type Rating')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.typeratings.store', 'class' => 'add_typerating', 'method'=>'POST', 'autocomplete' => false]) }}
      @include('admin.typeratings.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
@include('admin.typeratings.scripts')
