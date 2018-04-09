@extends('app')
@section('title', 'Pilots')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>Pilots</h2>
        @include('users.table')
    </div>
</div>
    <div class="row">
        <div class="col-12 text-center">
            {{ $users->links('pagination.default') }}
        </div>
    </div>
@endsection
