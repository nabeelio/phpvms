@extends('installer::app')
@section('title', 'Install phpVMS')

@section('content')
  <p>Press continue to start</p>
  {{ Form::open(['route' => 'installer.step1post', 'method' => 'post']) }}
  <p style="text-align: right">
    {{ Form::submit('Start >>', ['class' => 'btn btn-success']) }}
  </p>
  {{ Form::close() }}
@endsection
