@foreach($pireps as $pirep)
  <tr class="hover:bg-gray-200">
    <td class="text-base text-center py-3">{{ $pirep->ident }}</td>
    <td class="text-base text-center py-3">{{ $pirep->dpt_airport->name }} | {{ $pirep->dpt_airport->icao }}</td>
    <td class="text-base text-center py-3">{{ $pirep->arr_airport->name }} | {{ $pirep->arr_airport->icao }}</td>
    <td class="text-base text-center py-3">@minutestotime($pirep->flight_time) / {{ $pirep->distance }}</td>
    <td class="text-base text-center py-3">
      @if($pirep->state === PirepState::ACCEPTED)
        <span class="bg-green-600 text-white text-xs font-medium px-2 py-1 rounded-sm">Accepted</span>
      @elseif($pirep->state === PirepState::PENDING)
        <span class="bg-yellow-600 text-white text-xs font-medium px-2 py-1 rounded-sm">Under Review</span>
      @elseif($pirep->state === PirepState::REJECTED)
        <span class="bg-red-600 text-white text-xs font-medium px-2 py-1 rounded-sm">Rejected</span>
      @endif
      </td>
    <td class="text-base text-center py-3">
      <a href="{{ route('frontend.pireps.show', [$pirep->id]) }}" class="font-semibold hover:underline">></a>
    </td>
  </tr>
@endforeach