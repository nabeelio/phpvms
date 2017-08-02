@extends('layouts.default.app')

@section('content')
<div class="row">
    @include('flash::message')
    <div class="col-sm-9">
        <h2 class="description">flights</h2>
        @include('layouts.default.flights.table')
    </div>
    <div class="col-sm-3">
        <h2 class="description">search</h2>
        <div class="card">
            <div class="card-block" style="min-height: 0px">
            </div>
        </div>
    </div>
</div>
@endsection

