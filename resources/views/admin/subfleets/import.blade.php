@extends('admin.app')
@section('title', 'Import Subfleets')
@section('content')
    @include('admin.shared.import', ['route' => 'admin.subfleets.import'])
@endsection
