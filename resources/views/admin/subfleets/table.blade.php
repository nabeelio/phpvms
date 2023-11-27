<div class="content table-responsive table-full-width">
  <table class="table table-hover table-responsive" id="subfleets-table">
    <thead>
      <th>@sortablelink('name', 'Name')</th>
      <th>@sortablelink('airline.name', 'Airline')</th>
      <th>@sortablelink('type', 'Type Code')</th>
      <th>@sortablelink('hub_id', 'Base')</th>
      <th>Aircraft</th>
      <th class="text-right">Actions</th>
    </thead>
    <tbody>
      @foreach($subfleets as $subfleet)
        <tr>
          <td>{{ $subfleet->name }}</td>
          <td>{{ optional($subfleet->airline)->name }}</td>
          <td>{{ $subfleet->type }}</td>
          <td>{{ $subfleet->hub_id }}</td>
          <td>{{ $subfleet->aircraft->count() }}</td>
          <td class="text-right">
            {{ Form::open(['route' => ['admin.subfleets.destroy', $subfleet->id], 'method' => 'delete']) }}

            <a href="{{ route('admin.aircraft.index') }}?subfleet={{$subfleet->id}}" class='btn btn-sm btn-info text-black'>Manage Aircraft</a>
            <a href="{{ route('admin.subfleets.edit', [$subfleet->id]) }}" class='btn btn-sm btn-success text-black'>Edit Subfleet</a>

            {{ Form::button('Delete', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure?')"]) }}
            {{ Form::close() }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
