@extends('system.updater.app')
@section('title', 'Update Completed')

@section('content')
  <h2>phpvms updater</h2>
  <p>Update completed!.</p>

  {{ Form::open(['route' => 'update.complete', 'method' => 'GET']) }}
  <p style="text-align: right">
    {{ Form::submit('Finish >>', ['class' => 'btn btn-success']) }}
  </p>
  {{ Form::close() }}
@endsection
