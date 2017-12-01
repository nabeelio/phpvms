@extends('admin.app')
@section('title', 'Edit Flight')
@section('content')
    <div class="card  border-blue-bottom">
        @include('adminlte-templates::common.errors')
        <div class="content">
            {!! Form::model($flight, ['route' => ['admin.flights.store']]) !!}
                @include('admin.flights.fields')
            {!! Form::close() !!}
        </div>
    </div>
@endsection
