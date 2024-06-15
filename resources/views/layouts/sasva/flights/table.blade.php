
@foreach($flights as $flight)
  <tr class="hover:bg-gray-200">
    <td class="text-base text-center font-medium py-3 px-4">{{ $flight->ident }}</td>
    <td class="text-base text-left py-3 px-4">
      <span class="text-base font-medium text-blue-900 block">{{ optional($flight->dpt_airport)->name ?? $flight->dpt_airport_id }} ({{$flight->dpt_airport_id}})</span>
      <span class="text-sm text-gray-500 block">Departs {{ $flight->dpt_time }}</span>
    </td>
    <td class="text-base text-left py-3 px-4">
      <span class="text-base font-medium text-blue-900 block">{{ optional($flight->arr_airport)->name ?? $flight->arr_airport_id }} ({{$flight->arr_airport_id}})</span>
      <span class="text-sm text-gray-500 block">Arrives {{ $flight->arr_time }}</span>
    </td>
    <td class="text-base text-center py-3 px-4">@minutestotime($flight->flight_time) / {{ $flight->distance }}</td>
    <td class="text-base text-center py-3 px-4">
      @if (!setting('pilots.only_flights_from_current') || $flight->dpt_airport_id == $user->current_airport->icao)
        @if (!isset($saved[$flight->id]))
          <button class="py-2 px-3 bg-blue-900 text-white text-sm save_flight"
                  flight-id="{{ $flight->id }}"
                  flight-saved-class="bg-red-900"
                  type="button"
                  title="@lang('flights.addremovebid')">
                  Add booking
          </button>
        @else
          <button class="py-2 px-3 bg-red-900 text-white text-sm save_flight"
                  flight-id="{{ $flight->id }}"
                  flight-saved-class="bg-red-900"
                  type="button"
                  title="@lang('flights.addremovebid')">
                  Remove booking
          </button>
        @endif
      @endif
    </td>
  </tr>
@endforeach

