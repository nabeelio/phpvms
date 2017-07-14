<div id="ranks_table_wrapper">
    <table class="table table-responsive">
        <thead>
        <th>Name</th>
        <th>Hours</th>
        <th style="text-align: center;">Auto Approve Acars</th>
        <th style="text-align: center">Auto Approve Manual</th>
        <th style="text-align: center">Auto Promote</th>
        <th colspan="3" style="text-align: right;">Action</th>
        </thead>
        <tbody>
        @foreach($ranks as $rank)
            <tr>
                <td>{!! $rank->name !!}</td>
                <td>{!! $rank->hours !!}</td>
                <td style="text-align: center;">
                    <i class="fa fa-{{$rank->auto_approve_acars == 1?"check":""}}-square-o" aria-hidden="true"
                       style="color: {{$rank->auto_approve_acars==1?"darkgreen":"darkred"}};font-size:20px;"></i>
                </td>
                <td style="text-align: center;">
                    <i class="fa fa-{{$rank->auto_approve_manual == 1?"check":""}}-square-o" aria-hidden="true"
                       style="color: {{$rank->auto_approve_manual==1?"darkgreen":"darkred"}};font-size:20px;"></i>
                </td>
                <td style="text-align: center;">
                    <i class="fa fa-{{$rank->auto_promote == 1?"check":""}}-square-o" aria-hidden="true"
                       style="color: {{$rank->auto_promote==1?"darkgreen":"darkred"}};font-size:20px;"></i>
                </td>
                <td style="text-align: right;">
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
