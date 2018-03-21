@extends('admin.app')
@section('title', 'Import Aircraft')
@section('content')
    @include('admin.shared.import', ['route' => 'admin.aircraft.import'])
@endsection
