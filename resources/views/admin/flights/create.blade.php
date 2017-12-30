@extends('admin.app')
@section('title', 'Edit Flight')
@section('content')
    <div class="card  border-blue-bottom">
        @include('admin.flash.message')
        <div class="content">
            {!! Form::model($flight, ['route' => ['admin.flights.store']]) !!}
                @include('admin.flights.fields')
            {!! Form::close() !!}
        </div>
    </div>
@endsection
