<table class="">
    <tr>
        <td>
            @if($pirep->state === PirepState::PENDING
                || $pirep->state === PirepState::REJECTED)
                {!! Form::open(['url' => route('admin.pirep.status', ['id'=>$pirep->id]),
                                'method' => 'post',
                                'name' => 'accept_'.$pirep->id,
                                'id' => $pirep->id.'_accept',
                                'pirep_id' => $pirep->id,
                                'new_status' => PirepState::ACCEPTED,
                                'class' => 'pirep_submit_status']) !!}
                {!! Form::button('Accept', ['type' => 'submit', 'class' => 'btn btn-info']) !!}
                {!! Form::close() !!}
            @endif
        </td>
        <td>&nbsp;</td>
        <td>
            @if($pirep->state === PirepState::PENDING
                || $pirep->state === PirepState::ACCEPTED)
                {!! Form::open(['url' => route('admin.pirep.status', ['id'=>$pirep->id]),
                                'method' => 'post',
                                'name' => 'reject_'.$pirep->id,
                                'id' => $pirep->id.'_reject',
                                'pirep_id' => $pirep->id,
                                'new_status' => PirepState::REJECTED,
                                'class' => 'pirep_submit_status']) !!}
                {!! Form::button('Reject', ['type' => 'submit', 'class' => 'btn btn-danger']) !!}
                {!! Form::close() !!}
            @endif
        </td>
        <td>&nbsp;</td>
        <td>
            <a href="{!! route('admin.pireps.edit', [$pirep->id]) !!}"
               class='btn btn-sm btn-success btn-icon'>
                <i class="fa fa-pencil-square-o"></i></a>
        </td>
    </tr>
</table>
