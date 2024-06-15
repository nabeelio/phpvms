{{--

If you want to edit this, you can reference the CheckWX API docs:
https://api.checkwx.com/#metar-decoded

--}}

<div id="weather" class="bg-white shadow-sm mt-8">
  <div id="weather_head" class="border-b border-gray-100 p-4">
    <h2 class="text-xl font-medium">@lang('dashboard.weatherat', ['ICAO' => $metar['station']])</h2>
    <h6 class="text-sm text-gray-500">Updated {{$metar['observed_age']}}</h6>
  </div>
  <div id="weather_body" class="p-4">
    <table class="table-auto w-full">
      <tbody>
        <tr>
          <td class="text-base whitespace-nowrap font-medium px-2">Condition</td>
          <td class="text-base px-2">{{ $metar['category'] }}</td>
        </tr>
        <tr>
          <td class="text-base whitespace-nowrap font-medium px-2">Wind</td>
          <td class="text-base px-2">
            @if($metar['wind_speed'] < '1') Calm @else {{ $metar['wind_speed'] }} kts @lang('common.from') {{ $metar['wind_direction_label'] }} ({{ $metar['wind_direction']}}&deg;) @endif
            @if($metar['wind_gust_speed']) @lang('widgets.weather.guststo') {{ $metar['wind_gust_speed'] }} @endif
          </td>
        </tr>
        <tr>
          <td class="text-base whitespace-nowrap font-medium px-2">Visibility</td>
          <td class="text-base px-2">{{ $metar['visibility'][$unit_dist] }} {{$unit_dist}}</td>
        </tr>
        <tr>
          <td class="text-base whitespace-nowrap font-medium px-2">Clouds</td>
          <td class="text-base px-2">
            @if($unit_alt === 'ft') {{ $metar['clouds_report_ft'] }} @else {{ $metar['clouds_report'] }} @endif 
            @if($metar['cavok'] == 1) Ceiling and Visibility OK @endif
          </td>
        </tr>
        <tr>
          <td class="text-base whitespace-nowrap font-medium px-2">Temperature</td>
          <td class="text-base px-2">
            @if($metar['temperature'][$unit_temp]) {{ $metar['temperature'][$unit_temp] }} @else 0 @endif &deg;{{strtoupper($unit_temp)}}
            @if($metar['dew_point']), @lang('widgets.weather.dewpoint') @if($metar['dew_point'][$unit_temp]) {{ $metar['dew_point'][$unit_temp] }} @else 0 @endif &deg;{{strtoupper($unit_temp)}} @endif 
            @if($metar['humidity']), @lang('widgets.weather.humidity') {{ $metar['humidity'] }}%  @endif
          </td>
        </tr>
        <tr>
          <td class="text-base whitespace-nowrap font-medium px-2">Barometer</td>
          <td class="text-base px-2">{{ number_format($metar['barometer']['hPa']) }} hPa / {{ number_format($metar['barometer']['inHg'], 2) }} inHg</td>
        </tr>
        <tr>
          <td class="text-base whitespace-nowrap font-medium px-2">Raw METAR</td>
          <td class="text-base px-2">{{ $metar['raw'] }}</td>
        </tr>
        <tr>
          <td class="text-base whitespace-nowrap font-medium px-2">Raw TAF</td>
          <td class="text-base px-2">{{ $taf['raw'] }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
