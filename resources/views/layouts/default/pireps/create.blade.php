@extends('app')
@section('title', 'File Flight Report')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="description">New Flight Report</h2>
            @include('flash::message')
            {!! Form::open(['route' => 'frontend.pireps.store']) !!}

            @include("pireps.fields")

            {!! Form::close() !!}
        </div>
    </div>
@endsection

@include("pireps.scripts")
