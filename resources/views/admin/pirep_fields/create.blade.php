@extends('admin.app')
@section('title', 'Add PIREP Field')
@section('content')
    <div class="card border-blue-bottom">
        <div class="content">
            @include('admin.flash.message')
            {!! Form::open(['route' => 'admin.pirepfields.store']) !!}
                @include('admin.pirep_fields.fields')
            {!! Form::close() !!}
        </div>
    </div>
@endsection
