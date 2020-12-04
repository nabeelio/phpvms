<table>
  @foreach($pireps as $p)
    <tr>
      <td style="padding-right: 10px;">
        <span class="title">{{ $p->airline->code }} {{ $p->flight_number }}</span>
      </td>
      <td>
        <a href="{{route('frontend.airports.show', [$p->dpt_airport_id])}}">{{$p->dpt_airport_id}}</a>
        &nbsp;-&nbsp;
        <a href="{{route('frontend.airports.show', [$p->arr_airport_id])}}">{{$p->arr_airport_id}}</a>&nbsp;
        @if(!empty($p->aircraft))
          {{ optional($p->aircraft)->registration }} ({{ $p->aircraft->icao }})
        @endif
      </td>
    </tr>
  @endforeach
</table>
