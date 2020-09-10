@extends('admin.app')
@section('title', 'Add Expense')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.expenses.store', 'autocomplete' => false]) }}
      @include('admin.expenses.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
