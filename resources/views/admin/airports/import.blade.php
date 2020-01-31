@extends('admin.app')
@section('title', 'Import Airports')
@section('content')
  @include('admin.common.import', ['route' => 'admin.airports.import'])
@endsection
