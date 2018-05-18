@extends('app')
@section('title', __trans('frontend.errors.401title'))

@section('content')
<div class="container registered-page">
    <h3>@lang('frontend.errors.401header')</h3>
    <p>
		@foreach(trans('frontend.errors.401message') as $line)
			{!! str_replace(':link', config('app.url'), $line).'<br />' !!}
		@endforeach
    </p>
</div>
@endsection
