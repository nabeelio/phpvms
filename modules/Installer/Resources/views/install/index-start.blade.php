@extends('installer::app')
@section('title', 'Install phpVMS')

@section('content')
  <h2>phpvms installer</h2>
  <p>Press continue to start</p>
  {{ Form::open(['route' => 'installer.step1', 'method' => 'post']) }}
  <p style="text-align: right">
    {{ Form::submit('Start >>', ['class' => 'btn btn-success']) }}
  </p>
  {{ Form::close() }}
@endsection
