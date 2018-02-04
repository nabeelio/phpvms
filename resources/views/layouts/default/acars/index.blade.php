@extends("layouts.${SKIN_NAME}.app")
@section('title', 'live map')

@section('content')
    {{ Widget::liveMap() }}
@endsection

