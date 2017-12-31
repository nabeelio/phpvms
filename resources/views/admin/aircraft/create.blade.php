@extends('admin.app')
@section('title', 'Add Aircraft')
@section('content')
    <div class="card border-blue-bottom">
        <div class="content">
        @include('admin.flash.message')
        {!! Form::open(['route' => 'admin.aircraft.store']) !!}
            @include('admin.aircraft.fields')
        {!! Form::close() !!}
        </div>
    </div>
@endsection
