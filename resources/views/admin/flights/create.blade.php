@extends('admin.app')
@section('title', 'Add Flight')
@section('content')
    <div class="card border-blue-bottom">
        <div class="content">
            {!! Form::model($flight, ['route' => ['admin.flights.store']]) !!}
                @include('admin.flights.fields')
            {!! Form::close() !!}
        </div>
    </div>
@endsection
