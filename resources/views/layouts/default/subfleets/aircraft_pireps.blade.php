<table class="table table-sm table-striped text-center mb-0">
  <tr>
    <th class="text-left">@lang('pireps.flightident')</th>
    <th>@lang('common.departure')</th>
    <th>@lang('common.arrival')</th>
    <th>@lang('common.distance')</th>
    <th>@lang('pireps.flighttime')</th>
    <th>@lang('pireps.fuel_used')</th>
    <th>@lang('pireps.submitted')</th>
    <th>{{ trans_choice('common.pilot', 1) }}</th>
  </tr>
  @foreach($pireps as $pirep)
    <tr>
      <td class="text-left">{{ $pirep->airline->code }} {{ $pirep->ident }}</td>
      <td><a href="{{ route('frontend.airports.show', [$pirep->dpt_airport_id]) }}">{{ $pirep->dpt_airport_id }}</a></td>
      <td><a href="{{ route('frontend.airports.show', [$pirep->arr_airport_id]) }}">{{ $pirep->arr_airport_id }}</a></td>
      <td>
        @if($pirep->distance > 0)
          @if(setting('units.distance') === 'km')
            {{ number_format($pirep->distance * 1.852) }}
          @elseif(setting('units.distance') === 'mi')
            {{ number_format($pirep->distance * 1.151) }}
          @else
            {{ number_format($pirep->distance) }}
          @endif {{ setting('units.distance') }}
        @endif
      </td>
      <td>
        @if($pirep->flight_time > 0)
          @minutestotime($pirep->flight_time)
        @endif
      </td>
      <td>
        @if($pirep->fuel_used > 0)
          @if(setting('units.fuel') === 'kg')
            {{ number_format($pirep->fuel_used / 2.205) }}
          @else
            {{ number_format($pirep->fuel_used) }}
          @endif {{ setting('units.fuel') }}
        @endif
      </td>
      <td>{{ $pirep->submitted_at->diffForHumans() }}</td>
      <td><a href="{{ route('frontend.users.show.public', [$pirep->user_id]) }}">{{ $pirep->user->name_private }}</a></td>
    </tr>
  @endforeach
</table>
