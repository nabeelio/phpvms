<table class="table table-hover" id="users-table">
    <thead>
        <th></th>
        <th>Name</th>
        <th style="text-align: center"></th>
        <th style="text-align: center">Airline</th>
        <th style="text-align: center">Location</th>
        <th style="text-align: center">Flights</th>
        <th style="text-align: center">Hours</th>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td style="width: 80px;">
                <div class="photo-container">
                    <img class="rounded-circle"
                         src="{!! $user->gravatar(256) !!}&s=256"/>
                </div>
            </td>
            <td>
                <a href="{!! route('frontend.profile.show.public', ['id' => $user->id]) !!}">
                    {!! $user->name !!}
                </a>
            </td>
            <td align="center">
                @if(filled($user->country))
                    <span class="flag-icon flag-icon-{!! $user->country !!}"
                          title="{!! $country->alpha2($user->country)['name'] !!}"></span>
                @endif
            </td>
            <td class="text-center">{!! $user->airline->icao !!}</td>
            <td class="text-center">{!! $user->curr_airport_id !!}</td>
            <td align="center">{!! $user->flights !!}</td>
            <td align="center">{!! \App\Facades\Utils::minutesToTimeString($user->flight_time) !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>
