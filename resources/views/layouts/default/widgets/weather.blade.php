{{--

If you want to edit this, you can reference the CheckWX API docs:
https://api.checkwx.com/#metar-decoded

--}}
@if(!$metar)
    <p>METAR/TAF data could not be retrieved</p>
@else
    <table class="table table-striped">
        <tr>
            <td>Conditions</td>
            <td>
                {{ $metar->getCategory() }}
                @if($metar->getTemperature())
                    &nbsp;
                    {{$metar->getTemperature()}}°{{strtoupper($unit_temp)}}
                    ,&nbsp;
                @endif
                visibility
                {{$metar->getVisibility()}}{{$unit_dist}}
            </td>
        </tr>
        <tr>
            <td>Barometer</td>
            <td>{{ $metar->getPressure('hg') }} Hg
                / {{ $metar->getPressure('mb') }} MB
            </td>
        </tr>
        <tr>
            <td>Clouds</td>
            <td>
                @foreach($metar->getClouds() as $cloud)
                    <p>
                        {{$cloud['amount']}} @ {{$cloud['base_height']}} {{ $unit_alt }}
                    </p>
                @endforeach
            </td>
        </tr>
        <tr>
            <td>Wind</td>
            <td>
                {{$wind['speed']}} kts
                @ {{$wind['direction']}}°
                @if($wind['gusts'])
                    gusts to {{$wind['gusts']}}
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
            <td>{{$metar->getLastUpdate()}}</td>
        </tr>
    </table>
@endif
