@extends('admin.app')
@section('title', 'Add Aircraft')
@section('content')
    <div class="card border-blue-bottom">
        <div class="content">
        {!! Form::open(['route' => 'admin.aircraft.store']) !!}
            @include('admin.aircraft.fields')
        {!! Form::close() !!}
        </div>
    </div>
@endsection
