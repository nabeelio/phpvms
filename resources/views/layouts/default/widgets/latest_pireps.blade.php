<table>
    @foreach($pireps as $p)
        <tr>
            <td style="padding-right: 10px;">
                <span class="title">{{ $p->airline->code }}</span>
            </td>
            <td>
                {{ $p->dpt_airport_id }}-
                {{ $p->arr_airport_id }}&nbsp;
                @if($p->aircraft)
                    {{ $p->aircraft->name }}
                @endif
            </td>
        </tr>
    @endforeach
</table>
