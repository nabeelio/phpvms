@extends('admin.app')

@section('title', 'Settings')
@section('content')
  @include('flash::message')
  @include('admin.settings.table')
@endsection

@include('admin.settings.script')
