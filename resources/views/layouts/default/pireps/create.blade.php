@extends('app')
@section('title', __('pireps.fileflightreport'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2>@lang('pireps.newflightreport')</h2>
            @include('flash::message')
            {{ Form::open(['route' => 'frontend.pireps.store']) }}

            @include('pireps.fields')

            {{ Form::close() }}
        </div>
    </div>
@endsection

@include('pireps.scripts')
