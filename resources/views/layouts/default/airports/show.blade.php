@extends('app')
@section('title', $airport->full_name)

@section('content')
<div class="row" style="margin-bottom: 30px;">
    <div class="col-12">
        <h2>{{ $airport->full_name }}</h2>
    </div>

    {{-- Show the weather widget in one column --}}
    <div class="col-5">
        {{ Widget::Weather([
            'icao' => $airport->icao,
          ]) }}
    </div>

    {{-- Show the airspace map in the other column --}}
    <div class="col-7">
        {{ Widget::AirspaceMap([
            'width' => '100%',
            'height' => '400px',
            'lat' => $airport->lat,
            'lon' => $airport->lon,
          ]) }}
    </div>
</div>
<div class="row">
    {{-- There are files uploaded and a user is logged in--}}
    @if(count($airport->files) > 0 && Auth::check())
        <div class="col-12">
            <h3>{{ trans_choice('frontend.global.download', 2) }}</h3>
            @include('downloads.table', ['files' => $airport->files])
        </div>
    @endif
</div>

<div class="row">
    <div class="col-6">
        <h5>@lang('frontend.airports.inboundflights')</h5>
        @if(!$inbound_flights)
            <div class="jumbotron text-center">
			@lang('frontend.airports.noflightfound')
            </div>
        @else
            <table class="table table-striped table-condensed">
                <thead>
                <tr>
                    <th class="text-left">@lang('frontend.airports.ident')</th>
                    <th class="text-left">@lang('frontend.global.from')</th>
                    <th>@lang('frontend.global.departure')</th>
                    <th>@lang('frontend.global.arrival')</th>
                </tr>
                </thead>
                @foreach($inbound_flights as $flight)
                <tr>
                    <td class="text-left">
                        <a href="{{ route('frontend.flights.show', [$flight->id]) }}">
                            {{ $flight->ident }}
                        </a>
                    </td>
                    <td class="text-left">{{ $flight->dpt_airport->name }}
                        (<a href="{{route('frontend.airports.show',
                         ['id'=>$flight->dpt_airport->icao])}}">{{$flight->dpt_airport->icao}}</a>)
                    </td>
                    <td>{{ $flight->dpt_time }}</td>
                    <td>{{ $flight->arr_time }}</td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="col-6">
        <h5>@lang('frontend.airports.outboundflights')</h5>
        @if(!$outbound_flights)
            <div class="jumbotron text-center">
			@lang('frontend.airports.noflightfound')
            </div>
        @else
            <table class="table table-striped table-condensed">
                <thead>
                <tr>
                    <th class="text-left">@lang('frontend.airports.ident')</th>
                    <th class="text-left">@lang('frontend.global.to')</th>
                    <th>@lang('frontend.global.departure')</th>
                    <th>@lang('frontend.global.arrival')</th>
                </tr>
                </thead>
                @foreach($outbound_flights as $flight)
                    <tr>
                        <td class="text-left">
                            <a href="{{ route('frontend.flights.show', [$flight->id]) }}">
                                {{ $flight->ident }}
                            </a>
                        </td>
                        <td class="text-left">{{ $flight->arr_airport->name }}
                            (<a href="{{route('frontend.airports.show',
                         ['id'=>$flight->arr_airport->icao])}}">{{$flight->arr_airport->icao}}</a>)
                        </td>
                        <td>{{ $flight->dpt_time }}</td>
                        <td>{{ $flight->arr_time }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
</div>
@endsection
