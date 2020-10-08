@extends('system.updater.app')
@section('title', 'Update phpVMS')

@section('content')
  <h2>phpvms updater</h2>
  <p>It seems like you're up to date!</p>
  {{ Form::open(['route' => 'update.complete', 'method' => 'GET']) }}

  <p style="text-align: right">
    {{ Form::submit('Complete >>', ['class' => 'btn btn-success']) }}
  </p>
  {{ Form::close() }}
@endsection
