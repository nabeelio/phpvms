@extends('installer::app')
@section('title', 'Update phpVMS')

@section('content')
  <h2>phpvms updater</h2>
  <p>Click run to complete the update to version {{ $version }}</p>
  {{ Form::open(['route' => 'update.update_download', 'method' => 'post']) }}
  <p style="text-align: right">
    {{ Form::submit('Run >>', ['class' => 'btn btn-success']) }}
  </p>
  {{ Form::close() }}
@endsection
