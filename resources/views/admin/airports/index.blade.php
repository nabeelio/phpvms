@extends('admin.app')

@section('title', 'Airports')
@section('actions')
    <li>
        <a href="{!! route('admin.airports.create') !!}">
            <i class="ti-plus"></i>
            Add New</a>
    </li>
@endsection

@section('content')
    <div class="card">
        @include('admin.airports.table')
    </div>
@endsection
@include('admin.airports.script')
