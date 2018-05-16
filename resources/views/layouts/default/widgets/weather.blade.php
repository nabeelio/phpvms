{{--

If you want to edit this, you can reference the CheckWX API docs:
https://api.checkwx.com/#metar-decoded

--}}
@if(!$metar)
    <p>{{ __('METAR/TAF data could not be retrieved') }}</p>
@else
    <table class="table table-striped">
        <tr>
            <td>{{ __('Conditions') }}</td>
            <td>
                {{ $metar['category'] }}
                {{ $metar['temperature'][$unit_temp] }}
                °{{strtoupper($unit_temp)}}
                @if($metar['visibility'])
                , {{ __('visibility') }} {{ $metar['visibility'][$unit_dist] }} {{$unit_dist}}
                @endif
                @if($metar['humidity'])
                    , {{ $metar['humidity'] }}% {{ __('humidity') }}
                @endif
                @if($metar['dew_point'])
                    , {{ __('dew point') }}
                    {{ $metar['dew_point'][$unit_temp] }}
                    °{{strtoupper($unit_temp)}}
                @endif
            </td>
        </tr>
        <tr>
            <td>{{ __('Barometer') }}</td>
            <td>
                {{ number_format($metar['barometer'], 2) }} hPa
                / {{ number_format($metar['barometer_in'], 2) }} inHg
            </td>
        </tr>
        @if($metar['clouds'])
            <tr>
                <td>{{ __('Clouds') }}</td>
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
            <td>{{ __('Wind') }}</td>
            <td>
                {{$metar['wind_speed']}} kts {{ __('from') }} {{$metar['wind_direction_label']}}
                ({{$metar['wind_direction']}}°)
                @if($metar['wind_gust_speed'])
					{{ __('gusts to').' '.$metar['wind_gust_speed'] }}
                @endif
            </td>
        </tr>
        <tr>
            <td>{{ __('METAR') }}</td>
            <td>
                <div style="line-height:1.5em;min-height: 3em;">
                    {{ $metar['raw'] }}
                </div>
            </td>
        </tr>
        @if($metar['remarks'])
            <tr>
                <td>{{ __('Remarks') }}</td>
                <td>
                    {{ $metar['remarks'] }}
                </td>
            </tr>
        @endif
        <tr>
            <td>{{ __('Updated') }}</td>
            <td>{{$metar['observed_time']}} ({{$metar['observed_age']}})</td>
        </tr>
    </table>
@endif
