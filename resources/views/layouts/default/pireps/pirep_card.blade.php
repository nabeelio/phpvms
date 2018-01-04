<div class="card border-blue-bottom">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-sm-2 text-center">
                <h5>
                    <a class="text-c" href="{!! route('frontend.pireps.show', [$pirep->id]) !!}">
                        {!! $pirep->airline->code !!}
                        @if($pirep->flight_id)
                            {!! $pirep->flight->flight_number !!}
                        @else
                            {!! $pirep->flight_number !!}
                        @endif
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
                        <table width="100%">
                            <tr>
                                <td width="20%" nowrap><span class="title">DEP&nbsp;</span></td>
                                <td>{!! $pirep->dpt_airport_id !!}</td>
                            </tr>
                            <tr>
                                <td nowrap><span class="title">ARR&nbsp;</span></td>
                                <td>{!! $pirep->arr_airport_id !!}&nbsp;</td>
                            </tr>
                            <tr>
                                <td nowrap><span class="title">Flight Time&nbsp;</span></td>
                                <td>{!! Utils::minutesToTimeString($pirep->flight_time) !!}</td>
                            </tr>
                            <tr>
                                <td nowrap><span class="title">Aircraft&nbsp;</span></td>
                                <td>{!! $pirep->aircraft->name !!}
                                    ({!! $pirep->aircraft->registration !!})</td>
                            </tr>
                            @if($pirep->level)
                            <tr>
                                <td nowrap><span class="title">Flight Level&nbsp;</span></td>
                                <td>{!! $pirep->level !!}</td>
                            </tr>
                            @endif
                            <tr>
                                <td nowrap><span class="title">Filed On:&nbsp;</span></td>
                                <td>{!! show_datetime($pirep->created_at) !!}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
