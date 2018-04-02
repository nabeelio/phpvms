@extends('app')
@section('title', 'downloads')

@section('content')
    @include('flash::message')
    <div class="row">
        <div class="row-sm-12">
            <h2 class="description">Downloads</h2>
        </div>
    </div>
    @foreach($grouped_files as $group => $files)
    <div class="row" style="margin-bottom: 40px;">
        <div class="col-12">
            <h4 class="description">{{ $group }}</h4>
        </div>

        <div class="col-12">
            @include('downloads.table', ['files' => $files])
        </div>
    </div>
    @endforeach
@endsection

