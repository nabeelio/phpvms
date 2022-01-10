<table class="table table-hover table-responsive" id="awards-table">
  <thead>
    <th>Name</th>
    <th>Description</th>
    <th>Image</th>
    <th class="text-center">Active</th>
    <th class="text-right">Action</th>
  </thead>
  <tbody>
    @foreach($awards->sortby('name', SORT_NATURAL) as $award)
      <tr>
        <td>
          <a href="{{ route('admin.awards.edit', [$award->id]) }}">{{ $award->name }}</a>
        </td>
        <td>
          {{ $award->description }}
        </td>
        <td>
          @if($award->image_url)
            <img src="{{ $award->image_url }}" name="{{ $award->name }}" alt="No Image Available" style="height: 100px"/>
          @else
            -
          @endif
        </td>
        <td class="text-center">
          @if($award->active)
            <i class="fas fa-check-circle fa-2x text-success"></i>
          @else 
            <i class="fas fa-times-circle fa-2x text-danger"></i>
          @endif
        </td>
        <td class="text-right">
          {{ Form::open(['route' => ['admin.awards.destroy', $award->id], 'method' => 'delete']) }}
          <a href="{{ route('admin.awards.edit', [$award->id]) }}" class='btn btn-sm btn-success btn-icon'>
            <i class="fas fa-pencil-alt"></i>
          </a>
          {{ Form::button('<i class="fa fa-times"></i>', [
                  'type' => 'submit',
                  'class' => 'btn btn-sm btn-danger btn-icon',
                  'onclick' => "return confirm('Are you sure you want to delete this award?')"
          ]) }}
          {{ Form::close() }}
        </td>
      </tr>
    @endforeach
  </tbody>
</table>