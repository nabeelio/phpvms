<div id="pirep_{!! $pirep->id !!}_container">
<div class="card border-blue-bottom pirep_card_container">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-sm-2 text-center">
                <h5>
                    <a class="text-c"
                       href="{!! route('admin.pireps.show', [$pirep->id]) !!}">
                        {!! $pirep->ident !!}
                    </a>
                </h5>
                <div>
                    @if($pirep->state === PirepState::PENDING)
                        <div class="badge badge-warning">
                    @elseif($pirep->state === PirepState::ACCEPTED)
                        <div class="badge badge-success">
                    @elseif($pirep->state === PirepState::REJECTED)
                        <div class="badge badge-danger">
                    @else
                        <div class="badge badge-info">
                    @endif
                    {!! PirepState::label($pirep->state) !!}</div>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-6">
                        <div>
                            <span class="description">DEP&nbsp;</span>
                            {!! $pirep->dpt_airport->icao !!}&nbsp;
                            <span class="description">ARR&nbsp;</span>
                            {!! $pirep->arr_airport->icao !!}&nbsp;
                        </div>
                        <div><span class="description">Flight Time&nbsp;</span>
                            {!! Utils::minutesToTimeString($pirep->flight_time) !!}
                        </div>
                        <div><span class="description">Aircraft&nbsp;</span>
                            {!! $pirep->aircraft->registration !!}
                            ({!! $pirep->aircraft->name !!})
                        </div>
                        <div>
                            <span class="description">Flight Level&nbsp;</span>
                            {!! $pirep->level !!}
                        </div>
                        <div>
                            <span class="description">File Date&nbsp;</span>
                            {!! show_datetime($pirep->created_at) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <span class="description">more data&nbsp;</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 ">
                <table class="pull-right">
                    <tr>
                        <td>
                        @if($pirep->state == PirepState::PENDING
                            || $pirep->state == PirepState::REJECTED)
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
                        @if($pirep->state == PirepState::PENDING
                            || $pirep->state == PirepState::ACCEPTED)
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
            </div>
        </div>
    </div>
</div>
</div>
