@extends('app')
@section('title', trans_choice('common.flight', 1).' '.$flight->ident)

@section('content')
<div class="row">
    <div class="col-8">
        <div class="row">
            <div class="col-12">
                <h2>{{ $flight->ident }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <tr>
                        <td>@lang('common.departure')</td>
                        <td>
                            {{ $flight->dpt_airport->name }}
                            (<a href="{{route('frontend.airports.show', [
                            'id' => $flight->dpt_airport->icao
                            ])}}">{{$flight->dpt_airport->icao}}</a>)
                            @ {{ $flight->dpt_time }}
                        </td>
                    </tr>

                    <tr>
                        <td>@lang('common.arrival')</td>
                        <td>
                            {{ $flight->arr_airport->name }}
                            (<a href="{{route('frontend.airports.show', [
                            'id' => $flight->arr_airport->icao
                            ])}}">{{$flight->arr_airport->icao}}</a>)
                            @ {{ $flight->arr_time }}</td>
                    </tr>
                    @if($flight->alt_airport_id)
                    <tr>
                        <td>@lang('flights.alternateairport')</td>
                        <td>
                            {{ $flight->alt_airport->name }}
                            (<a href="{{route('frontend.airports.show', [
                            'id' => $flight->alt_airport->icao
                            ])}}">{{$flight->alt_airport->icao}}</a>)
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td>@lang('flights.route')</td>
                        <td>{{ $flight->route }}</td>
                    </tr>

                    @if(filled($flight->notes))
                        <tr>
                            <td>{{ trans_choice('common.note', 2) }}</td>
                            <td>{{ $flight->notes }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @include('flights.map')
            </div>
        </div>
    </div>
    <div class="col-4">
        <h5>{{$flight->dpt_airport_id}} @lang('common.metar')</h5>
        {{ Widget::Weather([
            'icao' => $flight->dpt_airport_id,
          ]) }}
        <br />
        <h5>{{$flight->arr_airport_id}} @lang('common.metar')</h5>
        {{ Widget::Weather([
            'icao' => $flight->arr_airport_id,
          ]) }}
        @if ($flight->alt_airport_id)
        <br />
        <h5>{{$flight->alt_airport_id}} @lang('common.metar')</h5>
        {{ Widget::Weather([
            'icao' => $flight->alt_airport_id,
          ]) }}
        @endif
    </div>
</div>
@endsection
