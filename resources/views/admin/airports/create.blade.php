@extends('admin.app')
@section('title', "Add Airport")
@section('content')
<div class="card border-blue-bottom">
    <div class="content">
        @include('adminlte-templates::common.errors')
        {!! Form::open(['route' => 'admin.airports.store']) !!}
        @include('admin.airports.fields')
        {!! Form::close() !!}
    </div>
</div>
@endsection
