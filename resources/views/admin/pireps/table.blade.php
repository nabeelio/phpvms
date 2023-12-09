{{-- @each('admin.pireps.pirep_card', $pireps, 'pirep') --}}
<div class="content table-responsive table-full-width">
  <table class="table table-hover" id="flights-table">
    <thead>
      <th>@sortablelink('state', 'State')</th>
      <th>@sortablelink('user.name', 'Pilot')</th>
      <th>@sortablelink('flight_number', 'Flight #')</th>
      <th>@sortablelink('aircraft.registration', 'Aircraft')</th>
      <th>@sortablelink('dpt_airport_id', 'Dep')</th>
      <th>@sortablelink('arr_airport_id', 'Arr')</th>
      <th>@sortablelink('flight_time', 'Time')</th>
      <th>@sortablelink('distance', 'Distance')</th>
      <th>@sortablelink('score', 'Score')</th>
      <th>@sortablelink('source', 'Source')</th>
      <th>@sortablelink('submitted_at', 'Submitted')</th>
      <th class="text-right">Actions</th>
    </thead>
    <tbody>
      @foreach($pireps as $pirep)
        <tr>
          <td>
            <div id="pirep_{{ $pirep->id }}_status_container">
              @php
                $PirepStateClass = "badge badge-info" ;
                if($pirep->state === PirepState::PENDING ) { $PirepStateClass = "badge badge-warning" ; }
                if($pirep->state === PirepState::ACCEPTED ) { $PirepStateClass = "badge badge-success" ; }
                if($pirep->state === PirepState::REJECTED ) { $PirepStateClass = "badge badge-danger" ; }
              @endphp
              <div class="{{ $PirepStateClass }}">{{ PirepState::label($pirep->state) }}</div>
            </div>
          </td>
          <td>
            <a href="{{ route('admin.users.edit', [$pirep->user->id]) }}">
              {{ $pirep->user_id.' | '.optional($pirep->user)->name_private }}
            </a>
          </td>
          <td>
            <a href="{{ route('admin.pireps.edit', [$pirep->id]) }}">{{$pirep->ident}}</a>
          </td>
          <td>
            @if($pirep->aircraft)
              {{ $pirep->aircraft->ident }}
            @else
              {{ $pirep->aircraft_id }}
            @endif
          </td>
          <td>{{ $pirep->dpt_airport_id }}</td>
          <td>{{ $pirep->arr_airport_id }}</td>
          <td>@minutestotime($pirep->flight_time)</td>
          <td>{{ round($pirep->distance->local()).' '.setting('units.distance') }}</td>
          <td>{{ $pirep->score }}</td>
          <td>
            {{ PirepSource::label($pirep->source) }}
            @if(filled($pirep->source_name))
              ({{ $pirep->source_name }})
            @endif
          </td>
          <td>{{ $pirep->submitted_at->format('d.M.Y H:i') }}</td>
          <td class="text-right">
            <div id="pirep_{{ $pirep->id }}_actionbar" class="pull-right">
              @include('admin.pireps.actions', ['pirep' => $pirep, 'on_edit_page' => false])
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

