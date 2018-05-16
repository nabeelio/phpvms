@extends('app')
@section('title', __trans_choice('Pilot', 2))

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>{{ __trans_choice('Pilot', 2) }}</h2>
        @include('users.table')
    </div>
</div>
    <div class="row">
        <div class="col-12 text-center">
            {{ $users->links('pagination.default') }}
        </div>
    </div>
@endsection
