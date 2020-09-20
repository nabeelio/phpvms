@extends('admin.app')
@section('title', "Edit \"$expense->name\"")
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($expense, ['route' => ['admin.expenses.update', $expense->id], 'method' => 'patch', 'autocomplete' => false]) }}
      @include('admin.expenses.fields')
      {{ Form::close() }}
    </div>
  </div>
@endsection
