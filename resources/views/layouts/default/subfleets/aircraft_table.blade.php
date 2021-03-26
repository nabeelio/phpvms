<table class="table table-sm table-striped text-left mb-0">
  <tr>
    <th>ICAO / IATA Type</th>
    <td>{{ $aircraft->icao ?? '' }} / {{ $aircraft->iata ?? '' }}</td>
  </tr>
  <tr>
    <th>@lang('common.configuration')</th>
    <td>
      @foreach($aircraft->subfleet->fares as $fare)
        @if(!$loop->first) &bull; @endif
        {{ $fare->name }}
        {{ number_format($fare->pivot->capacity) }}
        @if($fare->type == 1) {{ setting('units.weight') }} @else Pax @endif
      @endforeach
    </td>
  </tr>
  <tr>
    <th>@lang('common.status')</th>
    <td>{{ \App\Models\Enums\AircraftStatus::label($aircraft->status) }}</td>
  </tr>
  <tr>
    <th>@lang('common.state')</th>
    <td>{{ \App\Models\Enums\AircraftState::label($aircraft->state) }}</td>
  </tr>
  <tr>
    <th>@lang('common.airline')</th>
    <td>{{ $aircraft->subfleet->airline->name }}</td>
  </tr>
  <tr>
    <th>@lang('common.subfleet')</th>
    <td><a href="{{ route('frontend.subfleets.subfleet', [$aircraft->subfleet->id]) }}">{{ $aircraft->subfleet->name }}</a></td>
  </tr>
  @if($aircraft->subfleet->hub)
    <tr>
      <th>@lang('common.hub')</th>
      <td><a href="{{ route('frontend.airports.show', [$aircraft->subfleet->hub_id]) }}">{{ $aircraft->subfleet->hub->name }}</a></td>
    </tr>
  @endif
  <tr>
    <th>@lang('common.location')</th>
    <td><a href="{{ route('frontend.airports.show', [$aircraft->airport_id]) }}">{{ $aircraft->airport->name }}</a></td>
  </tr>
  @if($aircraft->flight_time)
    <tr>
      <th>@lang('pireps.flighttime')</th>
      <td>@minutestotime($aircraft->flight_time)</td>
    </tr>
  @endif
  @if($aircraft->fuel_onboard > 0)
    <tr>
      <th>@lang('common.fuelonboard')</th>
      <td>
        @if(setting('units.fuel') == 'kg')
          {{ number_format($aircraft->fuel_onboard / 2.205) }}
        @else
          {{ number_format($aircraft->fuel_onboard) }}
        @endif {{ setting('units.fuel') }}
      </td>
    </tr>
  @endif
  @if($aircraft->landing_time)
    <tr>
      <th>@lang('common.lastlanding')</th>
      <td>{{ Carbon::parse($aircraft->landing_time)->diffForHumans() }}</td>
    </tr>
  @endif
</table>
