@extends('admin.app')
@section('title', 'Expenses')

@section('actions')
  <li><a href="{{ route('admin.expenses.export') }}"><i class="ti-plus"></i>Export to CSV</a></li>
  <li><a href="{{ route('admin.expenses.import') }}"><i class="ti-plus"></i>Import from CSV</a></li>
  <li><a href="{{ route('admin.expenses.create') }}"><i class="ti-plus"></i>Add New</a></li>
@endsection

@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @if(!filled($expenses))
        <p class="text-center">
          There are no expenses
        </p>
      @else
        @include('admin.expenses.table')
      @endif
    </div>
  </div>
@endsection

