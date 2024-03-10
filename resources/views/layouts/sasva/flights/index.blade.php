@extends('app')
@section('title', trans_choice('common.flight', 2))

@section('content')
  <div class="pt-20 mb-4">
    <div class="bg-center bg-cover" style="background-image: url({{ public_asset('assets/sasva/media/landing_bg.jpg') }});">
      <div class="py-20 flex justify-center items-center flex-col" style="background-color: rgba(75, 85, 99, .8) !important">
        <h1 class="text-xl font-semibold text-white">Flights</h1>
        <h2 class="text-base text-white">Book your next flight</h2>
      </div>
    </div>
  </div>

  <div class="container mx-auto px-2">
    <div id="content" class="mb-7">
      <div class="flex flex-col md:flex-row">
        <div class="w-8/12">
            <div class="flex flex-col pb-2 md:pb-7 md:pr-8">
              <div class="bg-white rounded-md">
                <div class="py-3 px-6 rounded-t-md bg-blue-800 text-white">
                  <span class="text-base font-semibold">Flights</span>
                </div>
                <div class="py-3 px-6">
                  @include('flights.table')
                </div>
              </div>
            </div>
          </div>

        <div class="w-4/12">
          <h1>Hello!</h1>
        </div>
      </div>
    </div>
  </div>


  <div class="row">
    @include('flash::message')
    <div class="col-md-9">
      <h2>{{ trans_choice('common.flight', 2) }}</h2>
    </div>
    <div class="col-md-3">
      @include('flights.nav')
      @include('flights.search')
    </div>
  </div>
  <div class="row">
    <div class="col-12 text-center">
      {{ $flights->withQueryString()->links('pagination.default') }}
    </div>
  </div>
  @if (setting('bids.block_aircraft', false))
    @include('flights.bids_aircraft')
  @endif
@endsection

@include('flights.scripts')

