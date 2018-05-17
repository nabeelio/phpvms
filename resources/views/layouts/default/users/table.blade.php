<table class="table table-hover" id="users-table">
    <thead>
        <th></th>
        <th>{{ __('Name') }}</th>
        <th style="text-align: center"></th>
        <th style="text-align: center">{{ __('Airline') }}</th>
        <th style="text-align: center">{{ __('Location') }}</th>
        <th style="text-align: center">{{ __trans_choice('Flight', 2) }}</th>
        <th style="text-align: center">{{ __trans_choice('Hour', 2) }}</th>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td style="width: 80px;">
                <div class="photo-container">
				@if ($user->avatar == null)
					<img class="rounded-circle"
						 src="{{ $user->gravatar(256) }}&s=256"/>
				@else
					<img src="{{ $user->avatar->url }}">
				@endif
                </div>
            </td>
            <td>
                <a href="{{ route('frontend.profile.show.public', ['id' => $user->id]) }}">
                    {{ $user->name }}
                </a>
            </td>
            <td align="center">
                @if(filled($user->country))
                    <span class="flag-icon flag-icon-{{ $user->country }}"
                          title="{{ $country->alpha2($user->country)['name'] }}"></span>
                @endif
            </td>
            <td class="text-center">{{ $user->airline->icao }}</td>
            <td class="text-center">
                @if($user->current_airport)
                    {{ $user->curr_airport_id }}
                @else
                    -
                @endif
            </td>
            <td align="center">{{ $user->flights }}</td>
            <td align="center">{{ \App\Facades\Utils::minutesToTimeString($user->flight_time) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
