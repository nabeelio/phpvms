<table>
    @foreach($pireps as $p)
        <tr>
            <td style="padding-right: 10px;">
                <span class="title">{{ $p->airline->code }}</span>
            </td>
            <td>
                {{ $p->dpt_airport_id }}-
                {{ $p->arr_airport_id }}&nbsp;
                {{ $p->aircraft->name }}
            </td>
        </tr>
    @endforeach
</table>
