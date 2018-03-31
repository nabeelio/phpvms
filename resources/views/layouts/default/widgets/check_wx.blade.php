@if(!$data)
    <p>METAR/TAF data could not be retrieved</p>
@else
    <table class="table">
        <tr>
            <td colspan="2">
                <div style="line-height:1.5em;min-height: 3em;">
                    {{$data->raw_text}}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">Updated: {{$data->observed}}</td>
        </tr>
        <tr>
            <td>Conditions</td>
            <td>
                {{$data->flight_category}},
                @if($unit_temp === 'f')
                    {{$data->temperature->fahrenheit}}°F
                @else
                    {{$data->temperature->celsius}}°C
                @endif
                , visibility
                @if($unit_dist === 'km')
                    {{intval(str_replace(',', '', $data->visibility->meters)) / 1000}}
                @else
                    {{$data->visibility->miles}}
                @endif
                &nbsp;
                {{$data->humidity_percent}}% humidity
            </td>
        </tr>
        <tr>
            <td>Barometer</td>
            <td>{{ $data->barometer->hg }}hg/{{ $data->barometer->mb }}mb</td>
        </tr>
        <tr>
            <td>Clouds</td>
            <td>
                @foreach($data->clouds as $cloud)
                    <p>
                        {{$cloud->text}} @
                        @if($unit_alt === 'ft')
                            {{$cloud->base_feet_agl}}
                        @else
                            {{$cloud->base_meters_agl}}
                        @endif
                        {{$unit_alt}}
                    </p>
                @endforeach
            </td>
        </tr>
        <tr>
            <td>Wind</td>
            <td>
                {{$data->wind->speed_kts}}kts @ {{$data->wind->degrees}},
                @if(property_exists($data->wind, 'gusts_kts'))
                    gusts to {{$data->wind->gusts_kts}}
                @endif
            </td>
        </tr>
    </table>
@endif
