<div id="pirep_{!! $pirep->id !!}_container">
<div class="card border-blue-bottom pirep_card_container">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-sm-2 text-center">
                <h5>
                    <a class="text-c"
                       href="{!! route('admin.pireps.edit', [$pirep->id]) !!}">
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

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div id="pirep_{!! $pirep->id !!}_actionbar" class="pull-right">
                    @include('admin.pireps.actions')
                </div>
            </div>
        </div>
    </div>
</div>
</div>
