@extends('admin.app')
@section('title', 'Subfleets')

@section('actions')
  <li><a href="{{ route('admin.subfleets.export') }}"><i class="ti-plus"></i>Export to CSV</a>
  <li><a href="{{ route('admin.subfleets.import') }}"><i class="ti-plus"></i>Import from CSV</a></li>
  <li><a href="{{ route('admin.subfleets.create') }}"><i class="ti-plus"></i>Add New Subfleet</a></li>
@endsection

@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.flash.message')
      @include('admin.subfleets.table')
    </div>
  </div>
@endsection
