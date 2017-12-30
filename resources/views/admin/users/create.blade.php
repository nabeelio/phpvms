@extends('admin.app')
@section('title', 'Add User')
@section('content')
    <div class="card border-blue-bottom">
        <div class="content">
            @include('admin.flash.message')
            {!! Form::open(['route' => 'admin.airlines.store']) !!}
                @include('admin.airlines.fields')
            {!! Form::close() !!}
        </div>
    </div>
@endsection
