@extends('layouts.default.app')

@section('content')
<div class="row">
    @include('flash::message')
    <div class="col-sm-12">
        <h2 class="description">flights</h2>
        @include('layouts.default.flights.table')
    </div>
</div>
@endsection

