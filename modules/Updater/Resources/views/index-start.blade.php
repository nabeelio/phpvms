@extends('installer::app')
@section('title', 'Update phpVMS')

@section('content')
    <h2>phpvms updater</h2>
    <p>Press continue to check if there are any updates available.</p>
    {{ Form::open(['route' => 'update.step1', 'method' => 'post']) }}
    <p style="text-align: right">
        {{ Form::submit('Start >>', ['class' => 'btn btn-success']) }}
    </p>
    {{ Form::close() }}
@endsection
