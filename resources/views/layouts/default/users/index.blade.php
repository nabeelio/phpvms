@extends('layouts.default.app')
@section('title', 'Pilots')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="description">pilots</h2>
        @include('layouts.default.users.table')
    </div>
</div>
    <div class="row">
        <div class="col-12 text-center">
            {{ $users->links('layouts.default.pagination.default') }}
        </div>
    </div>
@endsection

