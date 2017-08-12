@extends('layouts.default.app')
@section('content')
<div class="container registered-page">
<h3>Page Not Found</h3>
<p>Well, this is embarrassing, the page you requested does not exist. Click <a href="{{ url()->previous() }}">here</a> to go back to the home page.

{{ $exception->getMessage() }}
</p>
</div>
@endsection
