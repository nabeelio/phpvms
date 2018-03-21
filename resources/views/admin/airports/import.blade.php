@extends('admin.app')
@section('title', 'Import Airports')
@section('content')
    @include('admin.shared.import', ['route' => 'admin.airports.import'])
@endsection
