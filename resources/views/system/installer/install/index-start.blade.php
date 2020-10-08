@extends('system.installer.app')
@section('title', 'Install phpVMS')

@section('content')
  <h3 class="text-center">Click on <b>Start</b> to Continue</h3>
  {{ Form::open(['route' => 'installer.step1post', 'method' => 'post']) }}
  <p style="text-align: right">
    {{ Form::submit('Start >>', ['class' => 'btn btn-success']) }}
  </p>
  {{ Form::close() }}
@endsection
