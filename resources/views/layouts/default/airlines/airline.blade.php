@extends('app')
@section('title', __('common.airline'))

@section('content')
  <div class="row">
    @include('flash::message')
  </div>

  <div class="row">
    {{-- Left --}}
    <div class="col-md-8">
      <h4>&bull; {{ $airline->name }} @lang('common.fleet')</h4>
      <div class="card mb-2">
        <div class="card-body">
          @if($airline->subfleets->count())
            @foreach($airline->subfleets->sortBy('name') as $subfleet)
              @if(!$loop->first)<hr class="m-0 p-0">@endif
              <table class="table table-sm table-borderless text-left mb-0">
                <tr>
                  <td>
                    <i class="fas fa-angle-double-down ml-1 mr-2" type="button" data-toggle="collapse" data-target="#list{{ $subfleet->id }}"
                      aria-expanded="false" aria-controls="list{{ $subfleet->id }}" title="Show/Hide Members"></i>
                    <a href="{{ route('frontend.subfleets.subfleet', [$subfleet->id]) }}">{{ $subfleet->name }}</a>
                  </td>
                  <td class="text-right">
                    {{ $subfleet->aircraft->count() ?? '--'}}
                  </td>
                </tr>
              </table>
              <div id="list{{ $subfleet->id }}" class="collapse">
                @include('subfleets.table')
              </div>
            @endforeach
          @endif
        </div>
      </div>
    </div>
    {{-- Right --}}
    <div class="col-md-4">
      <div class="text-center mb-2">
        @if($airline->logo)
          <img src="{{ $airline->logo }}" style="max-height: 50px;">
        @else
          <h4>{{ $airline->name }}</h4>
        @endif
      </div>

      <div class="text-center">
        ICAO: {{ $airline->icao }} @if($airline->iata)| IATA: {{ $airline->iata }}@endif
      </div>
      {{-- Show Downloads of Airline --}}
      @if(count($airline->files) > 0)
        <h4>{{ trans_choice('common.download', 2) }}</h4>
        <div class="card mb-2">
          @include('downloads.table', ['files' => $airline->files])
        </div>
      @endif
    </div>
  </div>

@endsection
