@extends('admin.app')
@section('title', 'Airports')

@section('actions')
  <li><a href="{{ route('admin.airports.export') }}"><i class="ti-plus"></i>Export to CSV</a></li>
  <li><a href="{{ route('admin.airports.import') }}"><i class="ti-plus"></i>Import from CSV</a></li>
  <li><a href="{{ route('admin.airports.create') }}"><i class="ti-plus"></i>Add New</a></li>
@endsection

@section('content')
  <div class="card">
    @include('admin.airports.search')
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.airports.table')
    </div>
  </div>

  <div class="row">
    <div class="col-12 text-center">
      {{ $airports->links('admin.pagination.default') }}
    </div>
  </div>
@endsection
@include('admin.airports.script')
