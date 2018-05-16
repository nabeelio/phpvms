@extends('app')
@section('title', __('Edit Flight Report'))
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2>{{ __('Edit Flight Report') }}</h2>
            @include('flash::message')
            {{ Form::model($pirep, [
                    'route' => ['frontend.pireps.update', $pirep->id],
                    'class' => 'form-group',
                    'method' => 'patch']) }}

            @include('pireps.fields')

            {{ Form::close() }}
        </div>
    </div>
@endsection
@include('pireps.scripts')
