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
                {{ $metar['category'] }}
                {{ $metar['temperature'][$unit_temp] }}
                °{{strtoupper($unit_temp)}}
                @if($metar['visibility'])
                , visibility {{ $metar['visibility'][$unit_dist] }} {{$unit_dist}}
                @endif
                @if($metar['humidity'])
                    , {{ $metar['humidity'] }}% humidity
                @endif
                @if($metar['dew_point'])
                    , dew point
                    {{ $metar['dew_point'][$unit_temp] }}
                    °{{strtoupper($unit_temp)}}
                @endif
            </td>
        </tr>
        <tr>
            <td>Barometer</td>
            <td>
                {{ $metar['barometer'] }} Hg
                / {{ $metar['barometer_in'] * 1000 }} MB
            </td>
        </tr>
        @if($metar['clouds'])
            <tr>
                <td>Clouds</td>
                <td>
                    @if($unit_alt === 'ft')
                        {{$metar['clouds_report_ft']}}
                    @else
                        {{ $metar['clouds_report'] }}
                    @endif
                </td>
            </tr>
        @endif
        <tr>
            <td>Wind</td>
            <td>
                {{$metar['wind_speed']}} kts from {{$metar['wind_direction_label']}}
                ({{$metar['wind_direction']}}°)
                @if($metar['wind_gust_speed'])
                    gusts to {{ $metar['wind_gust_speed'] }}
                @endif
            </td>
        </tr>
        <tr>
            <td>METAR</td>
            <td>
                <div style="line-height:1.5em;min-height: 3em;">
                    {{ $metar['raw'] }}
                </div>
            </td>
        </tr>
        @if($metar['remarks'])
            <tr>
                <td>Remarks</td>
                <td>
                    {{ $metar['remarks'] }}
                </td>
            </tr>
        @endif
        <tr>
            <td>Updated</td>
            <td>{{$metar['observed_time']}} ({{$metar['observed_age']}})</td>
        </tr>
    </table>
@endif
