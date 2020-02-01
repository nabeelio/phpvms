@extends('admin.app')
@section('title', 'Import Expenses')

@section('content')
  @include('admin.common.import', ['route' => 'admin.expenses.import'])
@endsection
