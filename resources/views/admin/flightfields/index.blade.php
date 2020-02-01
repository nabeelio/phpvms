@extends('admin.app')
@section('title', 'Flight Fields')
@section('actions')
  <li><a href="{{ route('admin.flightfields.create') }}"><i class="ti-plus"></i>Add Field</a></li>
  <li>
    <a href="{{ route('admin.flights.create') }}">
      <i class="ti-plus"></i>
      Add Flight</a>
  </li>
@endsection
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.flightfields.table')
    </div>
  </div>
@endsection

