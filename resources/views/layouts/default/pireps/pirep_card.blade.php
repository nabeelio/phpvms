<div class="card border-blue-bottom">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-12">
                <p class="float-right">
                    <a href="{{ route('frontend.pireps.edit', [
                            'id'    => $pirep->id,
                        ]) }}" class="btn btn-sm btn-info">edit</a>
                </p>
                <h5>
                    <a href="{{ route('frontend.pireps.show', [$pirep->id]) }}">
                    {{ $pirep->airline->code }}{{ $pirep->ident }}</a>
                    -
                    {{ $pirep->dpt_airport->name }}
                    (<a href="{{route('frontend.airports.show', [
                            'id' => $pirep->dpt_airport->icao
                            ])}}">{{$pirep->dpt_airport->icao}}</a>)
                    <span class="description">to</span>
                    {{ $pirep->arr_airport->name }}
                    (<a href="{{route('frontend.airports.show', [
                            'id' => $pirep->arr_airport->icao
                            ])}}">{{$pirep->arr_airport->icao}}</a>)
                </h5>
            </div>
            <div class="col-sm-2 text-center">
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
                        <table class="table-condensed" width="100%">
                            <tr>
                                <td nowrap><span class="title">Flight Time&nbsp;</span></td>
                                <td>{{ Utils::minutesToTimeString($pirep->flight_time) }}</td>
                            </tr>
                            <tr>
                                <td nowrap><span class="title">Aircraft&nbsp;</span></td>
                                <td>{{ $pirep->aircraft->name }}
                                    ({{ $pirep->aircraft->registration }})</td>
                            </tr>
                            @if($pirep->level)
                            <tr>
                                <td nowrap><span class="title">Flight Level&nbsp;</span></td>
                                <td>{{ $pirep->level }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td nowrap><span class="title">Filed On:&nbsp;</span></td>
                                <td>{{ show_datetime($pirep->created_at) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
