@extends('app')
@section('title', __('pireps.fileflightreport'))

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h2>@lang('pireps.newflightreport')</h2>
      @include('flash::message')
      @if(!empty($pirep))
        {{ Form::model($pirep, ['route' => 'frontend.pireps.store']) }}
      @else
        {{ Form::open(['route' => 'frontend.pireps.store']) }}
      @endif

      @include('pireps.fields')

      {{ Form::close() }}
    </div>
  </div>
@endsection

@include('pireps.scripts')
