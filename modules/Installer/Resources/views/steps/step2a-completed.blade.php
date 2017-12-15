@extends('installer::app')
@section('title', 'Database Setup')
@section('content')
<div style="align-content: center;">
    {!! Form::open(['route' => 'installer.complete', 'method' => 'GET']) !!}

    <pre class="prettyprint lang-html">
        <code class="language-bash">
        {!! $console_output !!}
        </code>
    </pre>

    <p style="text-align: right">
        {!! Form::submit('Complete Setup >>', ['class' => 'btn btn-success']) !!}
    </p>
    {!! Form::close() !!}
</div>
@endsection
