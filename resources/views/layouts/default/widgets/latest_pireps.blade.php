<table>
    @foreach($pireps as $p)
        <tr>
            <td style="padding-right: 10px;">
                <span class="title">{{ $p->airline->code }}</span>
            </td>
            <td>
              <a href="{{route('frontend.airports.show', [$p->dpt_airport_id])}}">{{$p->dpt_airport_id}}</a>
                &nbsp;-&nbsp;
              <a href="{{route('frontend.airports.show', [$p->arr_airport_id])}}">{{$p->arr_airport_id}}</a>&nbsp;
                @if($p->aircraft)
                    {{ $p->aircraft->name }}
                @endif
            </td>
        </tr>
    @endforeach
</table>
