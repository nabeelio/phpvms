@extends('admin.app')
@section('Add Rank')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.ranks.store', 'class' => 'add_rank', 'method'=>'POST', 'autocomplete' => false]) }}
      @include('admin.ranks.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
@include('admin.ranks.scripts')
