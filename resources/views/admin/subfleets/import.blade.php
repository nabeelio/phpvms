@extends('admin.app')
@section('title', 'Import Subfleets')
@section('content')
  @include('admin.common.import', ['route' => 'admin.subfleets.import'])
@endsection
