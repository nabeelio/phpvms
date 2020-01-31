@extends('admin.app')
@section('title', 'Import Fares')
@section('content')
  @include('admin.common.import', ['route' => 'admin.fares.import'])
@endsection
