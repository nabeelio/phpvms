@extends("layouts.${SKIN_NAME}.app")
@section('title', 'flights')

@section('content')
<div class="row">
    @include('flash::message')
    <div class="col-md-9">
        <h2 class="description">flights</h2>
        @include("layouts.${SKIN_NAME}.flights.table")
    </div>
    <div class="col-md-3">
        @include("layouts.${SKIN_NAME}.flights.search")
    </div>
</div>
<div class="row">
    <div class="col-12 text-center">
        {{ $flights->links("layouts.${SKIN_NAME}.pagination.default") }}
    </div>
</div>
@endsection

@include("layouts.${SKIN_NAME}.flights.scripts")

