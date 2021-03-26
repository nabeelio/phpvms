<table class="table table-sm table-striped text-center mb-0">
  <tr>
    <th class="text-left">@lang('common.registration') / @lang('common.name')</th>
    <th>ICAO Type</th>
    <th>@lang('common.hub')</th>
    <th>@lang('common.location')</th>
    <th>@lang('pireps.flighttime')</th>
    <th>@lang('common.lastlanding')</th>
    <th>@lang('common.fuelonboard')</th>
    <th>@lang('common.state')</th>
    <th>@lang('common.status')</th>
  </tr>
  @foreach($subfleet->aircraft as $aircraft)
    <tr>
      <td class="text-left">
        <a href="{{ route('frontend.subfleets.aircraft', [$aircraft->id]) }}">
          {{ $aircraft->registration }}  @if($aircraft->registration != $aircraft->name)'{{ $aircraft->name }}'@endif
        </a>
      </td>
      <td>{{ $aircraft->icao }}</td>
      <td>{{ $subfleetfleet->hub_id ?? '--'}}</td>
      <td><a href="{{ route('frontend.airports.show', [$aircraft->airport_id]) }}">{{ $aircraft->airport_id }}</a></td>
      <td>
        @if($aircraft->flight_time)
          @minutestotime($aircraft->flight_time)
        @endif
      </td>
      <td>
        @if($aircraft->landing_time)
          {{ Carbon::parse($aircraft->landing_time)->diffForHumans() }}
        @endif
      </td>
      <td>
        @if($aircraft->fuel_onboard > 0)
          @if(setting('units.weight') == 'kg')
            {{ number_format($aircraft->fuel_onboard / 2.205) }}
          @else
            {{ number_format($aircraft->fuel_onboard) }}
          @endif {{ setting('units.weight') }}
        @endif
      </td>
      <td><span class="badge badge-sm badge-info">{{ \App\Models\Enums\AircraftState::label($aircraft->state) }}</span></td>
      <td><span class="badge badge-sm badge-primary">{{ \App\Models\Enums\AircraftStatus::label($aircraft->status) }}</span></td>
    </tr>
  @endforeach
</table>
