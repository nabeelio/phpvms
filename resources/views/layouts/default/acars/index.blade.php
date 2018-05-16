@extends('app')
@section('title', __('Live Map'))

@section('content')
    {{ Widget::liveMap() }}
@endsection

