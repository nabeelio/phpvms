@extends('admin.app')
@section('title', 'Adding Field')
@section('content')
    <div class="card border-blue-bottom">
        <div class="content">
            @include('admin.flash.message')
            {!! Form::open(['route' => 'admin.pirepfields.store']) !!}
                @include('admin.pirepfields.fields')
            {!! Form::close() !!}
        </div>
    </div>
@endsection
