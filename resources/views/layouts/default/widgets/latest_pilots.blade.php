<table>
  @foreach($users as $u)
    <tr>
      <td style="padding-right: 10px;">
        <span class="title">{{ $u->ident }}</span>
      </td>
      <td>{{ $u->name }}</td>
    </tr>
  @endforeach
</table>
