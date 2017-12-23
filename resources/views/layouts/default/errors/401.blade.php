@extends('layouts.default.app')
@section('title', 'not authorized')
@section('content')
<div class="container registered-page">
<h3>Unauthorized</h3>
<p>Well, this is embarrassing, you are not authorized to access or perform this function. Click <a href="{{ url()->previous() }}">here</a> to go back to the home page.</p>
</div>
@endsection
