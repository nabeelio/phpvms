@extends('app')
@section('title', __trans_choice('Flight', 2))

@section('content')
<div class="row">
    @include('flash::message')
    <div class="col-md-9">
        <h2>{{ $title ?? __trans_choice('Flight', 2) }}</h2>
        @include('flights.table')
    </div>
    <div class="col-md-3">
        @include('flights.nav')
        @include('flights.search')
    </div>
</div>
<div class="row">
    <div class="col-12 text-center">
        {{ $flights->links('pagination.default') }}
    </div>
</div>
@endsection

@include('flights.scripts')

