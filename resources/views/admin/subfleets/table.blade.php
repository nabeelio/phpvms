<div class="content table-responsive table-full-width">
<table class="table table-hover table-responsive" id="subfleets-table">
    <thead>
        <th>Name</th>
        <th>Airline</th>
        <th>Type</th>
        <th></th>
    </thead>
    <tbody>
    @foreach($subfleets as $subfleet)
        <tr>
            <td>
                <a href="{!! route('admin.subfleets.edit', [$subfleet->id]) !!}">
                {!! $subfleet->name !!}
                </a>
            </td>
            <td>{!! $subfleet->airline->name !!}</td>
            <td>{!! $subfleet->type !!}</td>

            <td class="text-right">
                {!! Form::open(['route' => ['admin.subfleets.destroy', $subfleet->id], 'method' => 'delete']) !!}

                <a href="{!! route('admin.subfleets.edit', [$subfleet->id]) !!}" class='btn btn-sm btn-success btn-icon'>
                    <i class="fa fa-pencil-square-o"></i></a>

                {!! Form::button('<i class="fa fa-times"></i>',
                                 ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon',
                                  'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
