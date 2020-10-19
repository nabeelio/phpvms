@extends('system.updater.app')
@section('title', 'Update Completed')
@section('content')
  <div style="align-content: center;">
    {{ Form::open(['route' => 'update.complete', 'method' => 'GET']) }}

    <pre class="lang-sh">
        <code class="lang-sh">
            {{ $console_output }}
        </code>
    </pre>

    <p style="text-align: right">
      {{ Form::submit('Complete >>', ['class' => 'btn btn-success']) }}
    </p>
    {{ Form::close() }}
  </div>
@endsection
