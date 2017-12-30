@extends('admin.app')
@section('title', 'Add Subfleet')
@section('content')
    <div class="card border-blue-bottom">
        <div class="content">
            @include('admin.flash.message')
            {!! Form::open(['route' => 'admin.subfleets.store']) !!}
                @include('admin.subfleets.fields')
            {!! Form::close() !!}
        </div>
    </div>
@endsection
