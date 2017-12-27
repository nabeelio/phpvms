@extends('layouts.default.app')

@section('title', 'live map')
@section('content')
    @include('layouts.default.acars.map')
    <div class="clearfix" style="padding-top: 25px"></div>
    @include('layouts.default.acars.table')
@endsection

