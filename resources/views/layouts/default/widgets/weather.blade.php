{{--

If you want to edit this, you can reference the CheckWX API docs:
https://api.checkwx.com/#metar-decoded

--}}
@if(!$metar)
    <p>METAR/TAF data could not be retrieved</p>
@else
    <table class="table">
        <tr>
            <td>Conditions</td>
            <td>
                {{ $category }},&nbsp;
                @if($unit_temp === 'c')
                    {{ $metar->getAirTemperature()->getValue() }}
                @else
                    {{ round(($metar->getAirTemperature()->getValue() * 9/5) + 32, 2) }}
                @endif
                °{{strtoupper($unit_temp)}}

                @if($metar->getVisibility()->getVisibility())
                    , visibility
                    @if($unit_dist === 'km')
                        {{ $metar->getVisibility()->getVisibility()->getConvertedValue('m') / 1000 }}
                    @else
                        {{ $metar->getVisibility()->getVisibility()->getValue() }}
                    @endif
                    {{$unit_dist}}
                @endif
            </td>
        </tr>
        <tr>
            <td>Barometer</td>
            <td>{{ $metar->getPressure()->getValue() }} Hg
                / {{ round($metar->getPressure()->getValue() * 33.86) }} MB
            </td>
        </tr>
        <tr>
            <td>Clouds</td>
            <td>
                @foreach($metar->getClouds() as $cloud)
                    <p>
                        {{$cloud->getAmount()}} @
                        @if($unit_alt === 'ft')
                            {{$cloud->getBaseHeight()->getValue()}}
                        @else
                            {{$cloud->getBaseHeight()->getConvertedValue('m')}}
                        @endif
                        {{ $unit_alt }}
                    </p>
                @endforeach
            </td>
        </tr>
        <tr>
            <td>Wind</td>
            <td>
                {{$metar->getSurfaceWind()->getMeanSpeed()->getConvertedValue('kt')}} kts
                @ {{$metar->getSurfaceWind()->getMeanDirection()->getValue()}}°
                @if($metar->getSurfaceWind()->getSpeedVariations())
                    gusts to {{$metar->getSurfaceWind()->getSpeedVariations()->getConvertedValue('kt')}}
                @endif
            </td>
        </tr>
        <tr>
            <td>METAR</td>
            <td>
                <div style="line-height:1.5em;min-height: 3em;">
                    {{ $metar->getRawMetar() }}
                </div>
            </td>
        </tr>
        <tr>
            <td>Updated</td>
            <td>{{$metar->getTime()}}</td>
        </tr>
    </table>
@endif
