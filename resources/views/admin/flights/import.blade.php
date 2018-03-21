@extends('admin.app')
@section('title', 'Import Flights')
@section('content')
    @include('admin.shared.import', ['route' => 'admin.flights.import'])
@endsection
