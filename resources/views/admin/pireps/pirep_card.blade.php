<div id="pirep_{{ $pirep->id }}_container">
<div class="card border-blue-bottom pirep_card_container">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-sm-2 text-center">
                <h5>
                    <a class="text-c"
                       href="{{ route('admin.pireps.edit', [$pirep->id]) }}">
                        {{ $pirep->ident }}
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
                    {{ PirepState::label($pirep->state) }}</div>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-6">
                        <div>
                            <span class="description">
                                <b>DEP</b>&nbsp;
                                {{ $pirep->dpt_airport->icao }}&nbsp;
                                <b>ARR</b>&nbsp;
                            {{ $pirep->arr_airport->icao }}&nbsp;
                            </span>
                        </div>
                        <div>
                            <span class="description"><b>Flight Time</b>&nbsp;
                            {{ Utils::minutesToTimeString($pirep->flight_time) }}
                            </span>
                        </div>
                        <div><span class="description"><b>Aircraft</b>&nbsp;
                            {{ $pirep->aircraft->registration }}
                            ({{ $pirep->aircraft->name }})
                            </span>
                        </div>
                        @if(filled($pirep->level))
                        <div>
                            <span class="description"><b>Flight Level</b>&nbsp;
                            {{ $pirep->level }}
                            </span>
                        </div>
                        @endif
                        <div>
                            <span class="description"><b>Filed Using</b>&nbsp;
                                {{ PirepSource::label($pirep->source) }}
                                @if(filled($pirep->source_name))
                                    ({{ $pirep->source_name }})
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="description"><b>File Date</b>&nbsp;
                                {{ show_datetime($pirep->created_at) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div id="pirep_{{ $pirep->id }}_actionbar" class="pull-right">
                    @include('admin.pireps.actions', ['pirep' => $pirep, 'on_edit_page' => false])
                </div>
            </div>
        </div>
    </div>
</div>
</div>
