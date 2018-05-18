@extends('app')
@section('title', trans('frontend.global.livemap'))

@section('content')
    {{ Widget::liveMap() }}
@endsection

