<table>
    @foreach($users as $u)
        <tr>
            <td style="padding-right: 10px;">
                <span class="title">{{ $u->pilot_id }}</span>
            </td>
            <td>{{ $u->name }}</td>
        </tr>
    @endforeach
</table>
