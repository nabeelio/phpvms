@extends('admin.app')
@section('title', 'Aircraft')

@section('actions')
  <li><a href="{{ route('admin.aircraft.export') }}"><i class="ti-plus"></i>Export to CSV</a></li>
  <li><a href="{{ route('admin.aircraft.import') }}"><i class="ti-plus"></i>Import from CSV</a></li>
  {{--<li><a href="{{ url('/admin/subfleets') }}"><i class="ti-files"></i>Subfleets</a></li>--}}
  <li><a href="{{ route('admin.aircraft.create') }}?subfleet={{$subfleet_id}}">
      <i class="ti-plus"></i>New Aircraft</a>
  </li>
@endsection

@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.aircraft.table')
    </div>
  </div>
@endsection

