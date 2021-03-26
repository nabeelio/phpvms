@extends('app')
@section('title', trans_choice('common.fleet', 1))

@section('content')
  <div class="row">
    @include('flash::message')
  </div>

  <div class="row">
    <div class="col-md-9">
      <h4>&bull; {{ $subfleet->airline->name }} | {{ $subfleet->name }}</h4>
      <div class="card mb-2">
        <div class="card-body">
          @include('subfleets.table')
        </div>
      </div>
    </div>
    <div class="col-md-3">
      @if(count($subfleet->files) > 0)
        <h4>{{ trans_choice('common.download', 2) }}</h4>
        <div class="card">
          <div class="card-body">
            @include('downloads.table', ['files' => $subfleet->files])
          </div>
        </div>
      @endif
    </div>
  </div>

@endsection
