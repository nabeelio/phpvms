{{--@each('pireps.pirep_card', $pireps, 'pirep')--}}

<table class="table table-condensed table-hover">
    <thead>
        <tr>
            <th>{{ __trans_choice('Flight', 1) }}</th>
            <th>{{ __('Departure') }}</th>
            <th>{{ __('Arrival') }}</th>
            <th>{{ __('Aircraft') }}</th>
            <th class="text-center">{{ __('Flight Time') }}</th>
            <th class="text-center">{{ __('Status') }}</th>
            <th>{{ __('Submitted') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>

    @foreach($pireps as $pirep)
        <tr>
            <td>
                <a href="{{ route('frontend.pireps.show', [
                    $pirep->id]) }}">{{ $pirep->airline->code }}{{ $pirep->ident }}</a>
            </td>
            <td>
                {{ $pirep->dpt_airport->name }}
                (<a href="{{route('frontend.airports.show', [
                    'id' => $pirep->dpt_airport->icao
                    ])}}">{{$pirep->dpt_airport->icao}}</a>)
            </td>
            <td>
                {{ $pirep->arr_airport->name }}
                (<a href="{{route('frontend.airports.show', [
                    'id' => $pirep->arr_airport->icao
                    ])}}">{{$pirep->arr_airport->icao}}</a>)
            </td>
            <td>{{ $pirep->aircraft->name }}</td>
            <td class="text-center">
                {{ (new \App\Support\Units\Time($pirep->flight_time)) }}
            </td>
            <td class="text-center">
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
            </td>
            <td>
                @if(filled($pirep->submitted_at))
                    {{ $pirep->submitted_at->diffForHumans() }}
                @endif
            </td>
            <td>
                @if(!$pirep->read_only)
                <a href="{{ route('frontend.pireps.edit', [
                        'id'    => $pirep->id,
                    ]) }}">{{ __('Edit') }}</a>
                @endif
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
