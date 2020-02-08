@extends('admin.app')
@section('title', 'Import Aircraft')
@section('content')
  @include('admin.common.import', ['route' => 'admin.aircraft.import'])
@endsection
