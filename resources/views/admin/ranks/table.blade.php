<div id="ranks_table_wrapper">
    <table class="table table-responsive">
        <thead>
        <th>Name</th>
        <th>Hours</th>
        <th>Auto Approve Acars</th>
        <th>Auto Approve Manual</th>
        <th>Auto Promote</th>
        <th colspan="3">Action</th>
        </thead>
        <tbody>
        @foreach($ranks as $rank)
            <tr>
                <td>{!! $rank->name !!}</td>
                <td>{!! $rank->hours !!}</td>
                <td>{!! $rank->auto_approve_acars !!}</td>
                <td>{!! $rank->auto_approve_manual !!}</td>
                <td>{!! $rank->auto_promote !!}</td>
                <td>
                    {!! Form::open(['route' => ['admin.ranks.destroy', $rank->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        {{--<a href="{!! route('admin.ranks.show', [$rank->id]) !!}"
                           class='btn btn-default btn-xs'><i
                                    class="glyphicon glyphicon-eye-open"></i></a>--}}
                        <a href="{!! route('admin.ranks.edit', [$rank->id]) !!}"
                           class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
