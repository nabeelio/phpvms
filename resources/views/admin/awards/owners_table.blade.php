<table class="table table-striped table-responsive" id="owners-table">
  <thead>
    <th>@sortablelink('user.name', 'User')</th>
    <th>@sortablelink('created_at', 'Issued At')</th>
    <th>&nbsp;</th>
  </thead>
  <tbody>
    @foreach($owners as $ow)
      <tr>
        <td>
          @if(filled($ow->user))
            <a href="{{ route('admin.users.edit', [$ow->user->id]) }}">{{ $ow->user->name_private.' ('.$ow->user->ident.')' }}</a>
          @else
            Deleted User
          @endif
        </td>
        <td>{{ $ow->created_at->format('d.M.Y H:i') }}</td>
        <td>&nbsp;</td>
      </tr>
    @endforeach
  </tbody>
</table>