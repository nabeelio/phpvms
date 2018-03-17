<table class="table table-hover table-responsive" id="awards-table">
    <thead>
        <th>Title</th>
        <th class="text-center">Description</th>
        <th class="text-center">Image</th>
        <th class="text-right">Action</th>
    </thead>
    <tbody>
    @foreach($awards as $award)
        <tr>
            <td>{!! $award->title !!}</td>
            <td class="text-center">{!! $award->description !!}</td>
            <td class="text-center"><img src="{!! $award->image !!}" name="{!! $award->title !!}" alt="No Image Available" /></td>
            <td class="text-right">
                {!! Form::open(['route' => ['admin.awards.destroy', $award->id], 'method' => 'delete']) !!}
                <a href="{!! route('admin.awards.edit', [$award->id]) !!}" class='btn btn-sm btn-success btn-icon'><i class="fa fa-pencil-square-o"></i></a>
                {!! Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure you want to delete this award?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
