{{--

If you want to edit this, you can reference the CheckWX API docs:
https://api.checkwx.com/#metar-decoded

--}}
@if(!$data)
    <p>METAR/TAF data could not be retrieved</p>
@else
    <table class="table">
        <tr>
            <td>Conditions</td>
            <td>
                {{$data->flight_category}},
                @if($unit_temp === 'f')
                    {{$data->temperature->fahrenheit}}°F
                @else
                    {{$data->temperature->celsius}}°C
                @endif

                @if($data->visibility->miles)
                    , visibility
                    @if($unit_dist === 'km')
                        {{intval(str_replace(',', '', $data->visibility->meters)) / 1000}}
                    @else
                        {{$data->visibility->miles}}
                    @endif
                &nbsp;
                @endif

                @if($data->humidity_percent)
                    {{$data->humidity_percent}}% humidity
                @endif
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
        <tr>
            <td>Metar</td>
            <td>
                <div style="line-height:1.5em;min-height: 3em;">
                    {{$data->raw_text}}
                </div>
            </td>
        </tr>
        <tr>
            <td>Updated</td>
            <td>{{$data->observed}}</td>
        </tr>
    </table>
@endif
