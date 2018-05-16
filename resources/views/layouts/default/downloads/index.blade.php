@extends('app')
@section('title', __trans_choice('Download', 2))

@section('content')
    @include('flash::message')
    <div class="row">
        <div class="row-sm-12">
            <h2>{{ __trans_choice('Download', 2) }}</h2>
        </div>
    </div>
    @if(!$grouped_files || \count($grouped_files) === 0)
        <div class="jumbotron text-center">{{ __('There are no downloads!') }}</div>
    @else
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
    @endif
@endsection

