@extends('app')
@section('title', __('common.airlines'))

@section('content')
  <div class="row">
    @include('flash::message')
    <h2 class="mb-1 mt-1">@lang('common.airlines')</h2>
  </div>

  @foreach($airlines->chunk(2) as $chunks)
    <div class="row">
      @foreach($chunks as $airline)
        <div class="col-md-6">
          {{-- Show Airline --}}
          <div class="card mb-2">
            <div class="card-header text-center">
              <h4><a href="{{ route('frontend.airlines.airline', [$airline->id]) }}">{{ $airline->name }}</a></h4>
            </div>
            <div class="card-body text-center">
              @if($airline->logo)
                <img src="{{ $airline->logo }}" style="max-height: 50px;">
              @endif
            </div>
            <div class="card-footer text-center">ICAO: {{ $airline->icao }} @if($airline->iata)| IATA: {{ $airline->iata }} @endif</div>
          </div>
          {{-- Show Downloads of Airline --}}
          @if(count($airline->files) > 0)
            <div class="card">
              <div class="card-header"><h4>{{ trans_choice('common.download', 2) }}</h4></div>
              <div class="card-body">
                @include('downloads.table', ['files' => $airline->files])
              </div>
            </div>
          @endif
        </div>
      @endforeach
    </div>
  @endforeach

@endsection
