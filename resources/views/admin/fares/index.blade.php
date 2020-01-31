@extends('admin.app')

@section('title', 'Fares')
@section('actions')
  <li><a href="{{ route('admin.fares.export') }}"><i class="ti-plus"></i>Export to CSV</a></li>
  <li><a href="{{ route('admin.fares.import') }}"><i class="ti-plus"></i>Import from CSV</a></li>
  <li><a href="{{ route('admin.fares.create') }}"><i class="ti-plus"></i>Add New</a></li>
@endsection

@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.fares.table')
    </div>
  </div>
@endsection

