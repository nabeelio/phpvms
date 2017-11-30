<table class="table table-hover table-responsive" id="airlines-table">
    <thead>
        <th>Name</th>
        <th>Email</th>
        <th class="text-center">Active</th>
        <th></th>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{!! $user->name !!}</td>
            <td>{!! $user->email !!}</td>
            <td class="text-center">
                @if($user->active == 1)
                    <span class="label label-success">Active</span>
                @else
                    <span class="label label-default">Inactive</span>
                @endif
            </td>
            <td class="text-right">
                {!! Form::open(['route' => ['admin.users.destroy', $user->id], 'method' => 'delete']) !!}
                <a href="{!! route('admin.users.edit', [$user->id]) !!}"
                   class='btn btn-sm btn-success btn-icon'><i class="fa fa-pencil-square-o"></i></a>
                {!! Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
