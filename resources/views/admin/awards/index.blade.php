@extends('admin.app')

@section('title', 'Awards')
@section('actions')
  <li>
    <a href="{!! route('admin.awards.create') !!}">
      <i class="ti-plus"></i>
      Add New
    </a>
  </li>
@endsection

@section('content')
  <div class="card">
    @include('admin.awards.table')
  </div>
@endsection

