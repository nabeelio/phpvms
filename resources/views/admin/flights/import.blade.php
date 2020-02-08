@extends('admin.app')
@section('title', 'Import Flights')
@section('content')
  @include('admin.common.import', ['route' => 'admin.flights.import'])
@endsection
