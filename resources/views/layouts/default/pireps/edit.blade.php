@extends('app')
@section('title', 'Edit Flight Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="description">Edit Flight Report</h2>
            @include('flash::message')
            {{ Form::model($pirep, ['route' => ['frontend.pireps.update', $pirep->id], 'method' => 'patch']) }}

            @include("pireps.fields")

            {{ Form::close() }}
        </div>
    </div>
@endsection
@include("pireps.scripts")
