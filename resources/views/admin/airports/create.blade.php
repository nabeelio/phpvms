@extends('admin.app')
@section('title', 'Add Airport')
@section('content')
<div class="card border-blue-bottom">
    <div class="content">
        @include('admin.flash.message')
        {!! Form::open(['route' => 'admin.airports.store', 'id' => 'airportForm']) !!}
        @include('admin.airports.fields')
        {!! Form::close() !!}
    </div>
</div>
@endsection
@include('admin.airports.script')
