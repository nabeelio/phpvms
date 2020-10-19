@extends('system.installer.app')
@section('title', 'Database Setup Completed')
@section('content')
  <div style="align-content: center;">
    {{ Form::open(['route' => 'installer.step3', 'method' => 'GET']) }}

    <pre class="lang-sh">
        <code class="lang-sh">
        {{--<code class="language-bash">--}}
          {{ $console_output }}
        </code>
    </pre>

    <p style="text-align: right">
      {{ Form::submit('Continue >>', ['class' => 'btn btn-success']) }}
    </p>
    {{ Form::close() }}
  </div>
@endsection
