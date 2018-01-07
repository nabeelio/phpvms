@extends('admin.app')

@section('title', 'Flights')
@section('actions')
    <li>
        <a href="{!! route('admin.flights.create') !!}">
            <i class="ti-plus"></i>
            Add New</a>
    </li>
@endsection

@section('content')
    <div class="card">
        @include('admin.flights.search')
    </div>

    <div class="card">
        @include('admin.flights.table')
    </div>

    <div class="row">
        <div class="col-12 text-center">
            {{ $flights->links('admin.pagination.default') }}
        </div>
    </div>
@endsection
