<table class="table table-hover table-responsive" id="fares-table">
  <thead>
  <th>Code</th>
  <th>Name</th>
  <th>Type</th>
  <th>Price</th>
  <th>Cost</th>
  <th>Notes</th>
  <th class="text-center">Active</th>
  <th class="text-right">Action</th>
  </thead>
  <tbody>
  @foreach($fares as $fare)
    <tr>
      <td><a href="{{ route('admin.fares.edit', [$fare->id]) }}">{{ $fare->code }}</a></td>
      <td>{{ $fare->name }}</td>
      <td>{{ \App\Models\Enums\FareType::label($fare->type) }}</td>
      <td>{{ $fare->price }}</td>
      <td>{{ $fare->cost }}</td>
      <td>{{ $fare->notes }}</td>
      <td class="text-center">
        @if($fare->active == 1)
          <span class="label label-success">Active</span>
        @else
          <span class="label label-default">Inactive</span>
        @endif
      </td>
      <td class="text-right">
        {{ Form::open(['route' => ['admin.fares.destroy', $fare->id], 'method' => 'delete']) }}
        <a href="{{ route('admin.fares.edit', [$fare->id]) }}" class='btn btn-sm btn-success btn-icon'>
          <i class="fas fa-pencil-alt"></i></a>
        {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) }}
        {{ Form::close() }}
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
