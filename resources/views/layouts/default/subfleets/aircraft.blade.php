@extends('app')
@section('title', trans_choice('common.aircraft', 1))

@section('content')
  <div class="row">
    <div class="col-md-8">
      <h4 class="mt-2">{{ $aircraft->registration }}  @if($aircraft->registration != $aircraft->name)'{{ $aircraft->name }}'@endif</h4>
      <div class="card">
        <div class="card-body">
          @include('subfleets.aircraft_table')
        </div>
      </div>
    </div>
    <div class="col-md-4">
      @if(count($aircraft->files) > 0)
        <h4 class="mt-2">{{ trans_choice('common.download', 2) }}</h4>
        <div class="card">
          <div class="card-body">@include('downloads.table', ['files' => $aircraft->files])</div>
        </div>
      @endif
    </div>
  </div>

  @if($pireps->count())
    <div class="row">
      <div class="col-md-8">
        <h4 class="mt-2">Latest Pireps</h4>
        <div class="card">
          <div class="card-body">
            @include('subfleets.aircraft_pireps')
          </div>
        </div>
      </div>
      <div class="col-md-4">
        {{-- Intentionally Left Blank --}}
      </div>
    </div>
  @endif
@endsection
