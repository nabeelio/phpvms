<div class="table-responsive">
  <table class="table table-hover table-striped">
    <thead>
    <tr>
      <th>{{ trans_choice('common.flight', 1) }}</th>
      <th>@lang('common.departure')</th>
      <th>@lang('common.arrival')</th>
      <th>@lang('common.aircraft')</th>
      <th class="text-center">@lang('flights.flighttime')</th>
      <th class="text-center">@lang('common.status')</th>
      <th>@lang('pireps.submitted')</th>
      <th></th>
    </tr>
    </thead>
    <tbody>

    @foreach($pireps as $pirep)
      <tr>
        <td>
          <a href="{{ route('frontend.pireps.show', [$pirep->id]) }}">{{ $pirep->ident }}</a>
        </td>
        <td>
          @if($pirep->dpt_airport){{ $pirep->dpt_airport->name }}@endif
                  (<a href="{{route('frontend.airports.show', [$pirep->dpt_airport_id])}}">{{$pirep->dpt_airport_id}}</a>)
        </td>
        <td>
          @if($pirep->arr_airport){{ $pirep->arr_airport->name }}@endif
                  (<a href="{{route('frontend.airports.show', [$pirep->arr_airport_id])}}">{{$pirep->arr_airport_id}}</a>)
        </td>
        <td>
          @if($pirep->aircraft)
            {{ optional($pirep->aircraft)->name }}
          @else
            -
          @endif
        </td>
        <td class="text-center">
          @minutestotime($pirep->flight_time)
        </td>
        <td class="text-center">
          @php
            $color = 'badge-info';
            if($pirep->state === PirepState::PENDING) {
                $color = 'badge-warning';
            } elseif ($pirep->state === PirepState::ACCEPTED) {
                $color = 'badge-success';
            } elseif ($pirep->state === PirepState::REJECTED) {
                $color = 'badge-danger';
            }
          @endphp
          <div class="badge {{ $color }}">{{ PirepState::label($pirep->state) }}</div>
        </td>
        <td>
          @if(filled($pirep->submitted_at))
            {{ $pirep->submitted_at->diffForHumans() }}
          @endif
        </td>
        <td>
          @if(!$pirep->read_only)
            <a href="{{ route('frontend.pireps.edit', [$pirep->id]) }}"
               class="btn btn-outline-info btn-sm"
               style="z-index: 9999"
               title="@lang('common.edit')">
              @lang('common.edit')
            </a>
          @endif
        </td>
      </tr>
    @endforeach

    </tbody>
  </table>
</div>
