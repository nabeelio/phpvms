@extends('app')
@section('title', trans('frontend.errors.404title'))

@section('content')
<div class="container registered-page">
    <h3>@lang('frontend.errors.404header')</h3>
    <p>
		@foreach(trans('frontend.errors.404message') as $line)
			{!! str_replace(':link', config('app.url'), $line).'<br />' !!}
		@endforeach
        {{ $exception->getMessage() }}
    </p>
</div>
@endsection
