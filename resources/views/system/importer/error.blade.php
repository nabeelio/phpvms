@extends('system.importer.app')
@section('title', 'Import Error!')

@section('content')
  <div style="align-content: center;">
    <h4>Error!</h4>
    <p class="text-danger">{{ $error }}</p>
  </div>
@endsection
