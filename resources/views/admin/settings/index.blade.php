@extends('admin.app')

@section('title', 'Settings')
@section('content')
    <div class="card">
        @include('flash::message')
        @include('admin.settings.table')
    </div>
@endsection

