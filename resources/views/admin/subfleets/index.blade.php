@extends('admin.app')

@section('title', 'Subfleets')
@section('actions')
    <li>
        <a href="{!! route('admin.subfleets.create') !!}">
            <i class="ti-plus"></i>Add New</a>
    </li>

@endsection
@section('content')
    <div class="card">
        @include('flash::message')
        @include('admin.subfleets.table')
    </div>
@endsection

