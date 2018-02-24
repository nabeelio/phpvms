@extends("layouts.${SKIN_NAME}.app")
@section('title', 'Edit Flight Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="description">Edit Flight Report</h2>
            @include('flash::message')
            {!! Form::model($pirep, ['route' => ['frontend.pireps.update', $pirep->id], 'method' => 'patch']) !!}

            @include("layouts.${SKIN_NAME}.pireps.fields")

            {!! Form::close() !!}
        </div>
    </div>
@endsection
@include("layouts.${SKIN_NAME}.pireps.scripts")
