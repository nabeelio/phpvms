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
        @include('admin.flights.table')
    </div>
@endsection

