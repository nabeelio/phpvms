@extends('system.updater.app')
@section('title', 'Update phpVMS')

@section('content')
  <h2>phpvms updater</h2>
  <p>Click run to complete the update!.</p>
  {{ Form::open(['route' => 'update.run_migrations', 'method' => 'post']) }}
  <p style="text-align: right">
    {{ Form::submit('Run >>', ['class' => 'btn btn-success']) }}
  </p>
  {{ Form::close() }}
@endsection
